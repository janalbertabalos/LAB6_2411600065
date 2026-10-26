<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryTransactionController extends Controller
{
    /**
     * Display a paginated list of inventory transactions with filters.
     */
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        // Filters
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);

        return view('transactions.index', compact('transactions', 'products'));
    }

    /**
     * Show the form for recording a new transaction.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'quantity']);
        $types = InventoryTransaction::TYPES;

        return view('transactions.create', compact('products', 'types'));
    }

    /**
     * Store a newly created transaction.
     * Observer handles stock update + low-stock notification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in(InventoryTransaction::TYPES)],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Extra stock-out check (also enforced in observer)
        if ($validated['type'] === InventoryTransaction::TYPE_STOCK_OUT) {
            $product = Product::findOrFail($validated['product_id']);
            if ($validated['quantity'] > $product->quantity) {
                return back()
                    ->withInput()
                    ->withErrors(['quantity' => "Insufficient stock. Available: {$product->quantity}."]);
            }
        }

        $validated['user_id'] = Auth::id();

        InventoryTransaction::create($validated);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Inventory transaction recorded successfully. Stock levels updated.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['product', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Remove the specified transaction (admin-only in a full app).
     * Note: Deleting does NOT reverse the stock change by default;
     * create a compensating transaction instead.
     */
    public function destroy(InventoryTransaction $transaction)
    {
        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction record deleted. Stock levels were not automatically reversed.');
    }

    /**
     * Show the stock adjustment form.
     */
    public function adjustmentForm()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'quantity']);

        return view('transactions.adjustment', compact('products'));
    }

    /**
     * Process a stock adjustment (physical count vs system count).
     */
    public function processAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'physical_count' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $systemQty = $product->quantity;
        $physical = $validated['physical_count'];
        $diff = $physical - $systemQty;

        if ($diff === 0) {
            return back()->with('info', 'No adjustment needed – physical count matches system quantity.');
        }

        $direction = $diff > 0 ? 'direction:increase' : 'direction:decrease';
        $absDiff = abs($diff);

        $notes = trim(($validated['notes'] ?? '') . ' | reason:' . $validated['reason'] . ' | ' . $direction . ' | system:' . $systemQty . ' physical:' . $physical);

        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => InventoryTransaction::TYPE_ADJUSTMENT,
            'quantity' => $absDiff,
            'reference_number' => 'ADJ-' . now()->format('YmdHis'),
            'notes' => $notes,
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', "Adjustment recorded. Quantity changed by {$diff} (from {$systemQty} to {$physical}).");
    }
}
