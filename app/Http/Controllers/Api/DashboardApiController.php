<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    /**
     * GET /api/dashboard/stats
     * Returns current inventory statistics as JSON for AJAX polling.
     */
    public function stats(): JsonResponse
    {
        $totalProducts = Product::count();
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();
        $totalInventoryValue = Product::query()
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as total_inventory_value')
            ->value('total_inventory_value');
        $totalTransactionsToday = InventoryTransaction::whereDate('created_at', today())->count();

        return response()->json([
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_inventory_value' => round($totalInventoryValue, 2),
            'transactions_today' => $totalTransactionsToday,
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /api/products/low-stock
     */
    public function lowStock(): JsonResponse
    {
        $products = Product::lowStock()
            ->orderBy('quantity')
            ->get(['id', 'name', 'sku', 'quantity', 'reorder_level', 'category'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'quantity' => $p->quantity,
                'reorder_level' => $p->reorder_level,
                'category' => $p->category,
                'status' => $p->stock_status,
                'status_color' => $p->stock_status_color,
                'url' => route('products.show', $p),
            ]);

        return response()->json(['data' => $products]);
    }

    /**
     * GET /api/transactions/recent
     */
    public function recentTransactions(): JsonResponse
    {
        $transactions = InventoryTransaction::with(['product:id,name,sku', 'user:id,name'])
            ->recent()
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'type_label' => $t->type_label,
                'type_badge' => $t->type_badge,
                'quantity' => $t->quantity,
                'signed_quantity' => $t->signed_quantity,
                'product_name' => $t->product->name ?? '—',
                'product_sku' => $t->product->sku ?? '—',
                'user_name' => $t->user->name ?? '—',
                'reference_number' => $t->reference_number,
                'created_at' => $t->created_at->toIso8601String(),
                'created_at_human' => $t->created_at->diffForHumans(),
                'url' => route('transactions.show', $t),
            ]);

        return response()->json(['data' => $transactions]);
    }

    /**
     * GET /api/products/{product}/quantity – helper for adjustment form / create form
     */
    public function productQuantity(Product $product): JsonResponse
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => $product->quantity,
            'reorder_level' => $product->reorder_level,
        ]);
    }
}
