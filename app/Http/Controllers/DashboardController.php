<?php

namespace App\Http\Controllers;

use App\Models\expenses;
use App\Models\purchase;
use App\Models\sale;
use App\Models\sale_details;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    /**
     * Display the business overview dashboard.
     */
    public function index(): View
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $now->copy()->endOfMonth()->format('Y-m-d');

        // 1. Calculate general overview metrics
        $businessBalance = myBalance();
        $customerReceivables = customerBalance();
        $supplierPayables = supplierBalance();
        $pendingOrders = sale::whereHas('details', function ($q) {
            $q->whereRaw('qty > delivered_qty');
        })->count();

        // 2. Calculate current month statistics
        $currentMonthSales = sale::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('total_bill');
        $currentMonthPurchases = purchase::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('total');
        $currentMonthExpenses = expenses::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');
        $currentMonthProfit = $currentMonthSales - $currentMonthPurchases - $currentMonthExpenses;

        // 3. Top 10 Products (Current Month)
        $topProducts = sale_details::selectRaw('product_id, sum(qty) as total_qty, sum(amount) as total_amount')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->take(10)
            ->get();

        // 4. Monthly Trend (last 6 months)
        $months = [];
        $salesData = [];
        $expensesData = [];
        $profitData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = (clone $now)->subMonths($i);
            $start = $date->copy()->startOfMonth()->format('Y-m-d');
            $end = $date->copy()->endOfMonth()->format('Y-m-d');
            
            $monthLabel = $date->format('M Y');
            $months[] = $monthLabel;

            $mSales = sale::whereBetween('date', [$start, $end])->sum('total_bill');
            $mPurchases = purchase::whereBetween('date', [$start, $end])->sum('total');
            $mExpenses = expenses::whereBetween('date', [$start, $end])->sum('amount');

            $salesData[] = round($mSales, 2);
            $expensesData[] = round($mExpenses, 2);
            $profitData[] = round($mSales - $mPurchases - $mExpenses, 2);
        }

        // 5. Recent Expenses
        $recentExpenses = expenses::with('category')->orderBy('created_at', 'desc')->take(6)->get();

        // 6. Recent Activities
        $recentActivities = collect();
        $recentSales = sale::with('customer')->orderBy('created_at', 'desc')->take(5)->get();
        foreach($recentSales as $s) {
            $recentActivities->push(['date' => $s->created_at, 'type' => 'Sale', 'desc' => 'Sale to '.($s->customer_name ?? ($s->customer->title ?? 'Walk-in')), 'amount' => $s->total_bill, 'color' => 'success']);
        }
        $recentPurchases = purchase::with('supplier')->orderBy('created_at', 'desc')->take(5)->get();
        foreach($recentPurchases as $p) {
            $recentActivities->push(['date' => $p->created_at, 'type' => 'Purchase', 'desc' => 'Purchase from '.($p->supplier->title ?? 'Unknown'), 'amount' => $p->total, 'color' => 'warning']);
        }
        $recentExp = expenses::with('category')->orderBy('created_at', 'desc')->take(5)->get();
        foreach($recentExp as $e) {
            $recentActivities->push(['date' => $e->created_at, 'type' => 'Expense', 'desc' => 'Expense: '.($e->category->name ?? 'Misc'), 'amount' => $e->amount, 'color' => 'danger']);
        }
        
        $recentActivities = $recentActivities->sortByDesc('date')->take(8)->values();

        return view('index', compact(
            'businessBalance',
            'customerReceivables',
            'supplierPayables',
            'pendingOrders',
            'currentMonthSales',
            'currentMonthPurchases',
            'currentMonthExpenses',
            'currentMonthProfit',
            'topProducts',
            'months',
            'salesData',
            'expensesData',
            'profitData',
            'recentExpenses',
            'recentActivities'
        ));
    }
}
