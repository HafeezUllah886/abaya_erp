<?php

namespace App\Http\Controllers;

use App\Models\expenses;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\sale;
use App\Models\sale_details;
use Illuminate\Http\Request;

class profitController extends Controller
{
    public function index()
    {
        return view('reports.profit.index');
    }

    public function details(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        // Overall Totals
        $sales = sale::whereBetween('date', [$from, $to])->sum('total_bill');
        $purchases = purchase::whereBetween('date', [$from, $to])->sum('total');
        $expenses = expenses::whereBetween('date', [$from, $to])->sum('amount');
        $profit = $sales - $purchases - $expenses;

        // Daily data for trend chart
        $salesData = sale::selectRaw('date, sum(total_bill) as total')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date')
            ->get()->keyBy('date');

        $purchasesData = purchase::selectRaw('date, sum(total) as total')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date')
            ->get()->keyBy('date');

        $expensesData = expenses::selectRaw('date, sum(amount) as total')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date')
            ->get()->keyBy('date');

        $dates = [];
        $salesChart = [];
        $purchasesChart = [];
        $expensesChart = [];
        $profitChart = [];

        $currentDate = strtotime($from);
        $endDate = strtotime($to);
        $daysCount = 0;

        while ($currentDate <= $endDate) {
            $dateStr = date('Y-m-d', $currentDate);
            $dates[] = date('d M', $currentDate);

            $saleAmt = isset($salesData[$dateStr]) ? (float) $salesData[$dateStr]->total : 0;
            $purchaseAmt = isset($purchasesData[$dateStr]) ? (float) $purchasesData[$dateStr]->total : 0;
            $expenseAmt = isset($expensesData[$dateStr]) ? (float) $expensesData[$dateStr]->total : 0;

            $salesChart[] = $saleAmt;
            $purchasesChart[] = $purchaseAmt;
            $expensesChart[] = $expenseAmt;
            $profitChart[] = $saleAmt - $purchaseAmt - $expenseAmt;

            $currentDate = strtotime('+1 day', $currentDate);
            $daysCount++;
        }

        $avgDailyProfit = $daysCount > 0 ? $profit / $daysCount : 0;
        $maxDailySales = ! empty($salesChart) ? max($salesChart) : 0;

        // Products Sold List
        $soldProducts = sale_details::selectRaw('product_id, sum(qty) as total_qty, sum(amount) as total_amount')
            ->whereBetween('date', [$from, $to])
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->get();

        // Top 5 Products for Chart
        $topSoldProductsNames = $soldProducts->take(5)->map(function ($item) {
            return $item->product->name ?? 'Unknown';
        })->toArray();
        $topSoldProductsAmounts = $soldProducts->take(5)->pluck('total_amount')->toArray();

        // Products Purchased List
        $purchasedProducts = purchase_details::selectRaw('product_id, sum(qty) as total_qty, sum(amount) as total_amount')
            ->whereBetween('date', [$from, $to])
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->get();

        // Expense by Categories
        $expenseByCategory = expenses::selectRaw('category_id, sum(amount) as total_amount')
            ->whereBetween('date', [$from, $to])
            ->with('category')
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->get();

        $expenseCategoryNames = $expenseByCategory->map(function ($item) {
            return $item->category->name ?? 'Unknown';
        })->toArray();
        $expenseCategoryAmounts = $expenseByCategory->pluck('total_amount')->toArray();

        return view('reports.profit.details', compact(
            'sales', 'purchases', 'expenses', 'profit', 'from', 'to',
            'dates', 'salesChart', 'purchasesChart', 'expensesChart', 'profitChart',
            'avgDailyProfit', 'maxDailySales',
            'soldProducts', 'purchasedProducts', 'expenseByCategory',
            'topSoldProductsNames', 'topSoldProductsAmounts',
            'expenseCategoryNames', 'expenseCategoryAmounts'
        ));
    }
}
