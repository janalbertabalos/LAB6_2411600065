<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with inventory statistics and recent activity.
     */
    public function index()
    {
        $totalProducts = Product::count();
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();
        $totalInventoryValue = Product::query()
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as total_inventory_value')
            ->value('total_inventory_value');

        $recentProducts = Product::orderBy('created_at', 'desc')->take(5)->get();
        $lowStockProducts = Product::lowStock()->orderBy('quantity')->take(8)->get();

        $recentTransactions = InventoryTransaction::with(['product', 'user'])
            ->recent()
            ->take(8)
            ->get();

        // Category breakdown for simple chart data
        $categoryCounts = Product::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        return view('dashboard.index', compact(
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalInventoryValue',
            'recentProducts',
            'lowStockProducts',
            'recentTransactions',
            'categoryCounts'
        ));
    }
}
