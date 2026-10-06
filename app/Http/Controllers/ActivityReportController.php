<?php

namespace App\Http\Controllers;

use App\Models\expenses;
use App\Models\issuePayment;
use App\Models\paymentReceiving;
use App\Models\purchase;
use App\Models\sale;
use App\Models\SaleDelivery;
use Illuminate\Http\Request;

class ActivityReportController extends Controller
{
    public function index()
    {
        return view('reports.activity.index');
    }

    public function details(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $activities = collect();

        // Sales
        $sales = sale::with('customer')->whereBetween('date', [$from, $to])->get();
        foreach ($sales as $s) {
            $activities->push([
                'date' => $s->date,
                'created_at' => $s->created_at,
                'type' => 'Sale',
                'ref' => $s->refID,
                'description' => 'Sale to '.($s->customer_name ?? ($s->customer->title ?? 'Walk-in')),
                'amount' => $s->total_bill,
                'color' => 'success',
            ]);
        }

        // Purchases
        $purchases = purchase::with('supplier')->whereBetween('date', [$from, $to])->get();
        foreach ($purchases as $p) {
            $activities->push([
                'date' => $p->date,
                'created_at' => $p->created_at,
                'type' => 'Purchase',
                'ref' => $p->refID,
                'description' => 'Purchase from '.($p->supplier->title ?? 'Unknown'),
                'amount' => $p->total,
                'color' => 'warning',
            ]);
        }

        // Expenses
        $expenses = expenses::with('category')->whereBetween('date', [$from, $to])->get();
        foreach ($expenses as $e) {
            $activities->push([
                'date' => $e->date,
                'created_at' => $e->created_at,
                'type' => 'Expense',
                'ref' => $e->refID,
                'description' => 'Expense: '.($e->category->name ?? 'Misc').($e->notes ? ' - '.strip_tags($e->notes) : ''),
                'amount' => $e->amount,
                'color' => 'danger',
            ]);
        }

        // Issue Payments
        $issuePayments = issuePayment::with(['to', 'account'])->whereBetween('date', [$from, $to])->get();
        foreach ($issuePayments as $ip) {
            $activities->push([
                'date' => $ip->date,
                'created_at' => $ip->created_at,
                'type' => 'Payment Issued',
                'ref' => $ip->refID,
                'description' => 'Paid to '.($ip->to->title ?? 'Unknown').' via '.($ip->account->title ?? 'Unknown'),
                'amount' => $ip->amount,
                'color' => 'secondary',
            ]);
        }

        // Payment Receivings
        $paymentReceivings = paymentReceiving::with(['from', 'account'])->whereBetween('date', [$from, $to])->get();
        foreach ($paymentReceivings as $pr) {
            $activities->push([
                'date' => $pr->date,
                'created_at' => $pr->created_at,
                'type' => 'Payment Received',
                'ref' => $pr->refID,
                'description' => 'Received from '.($pr->from->title ?? 'Unknown').' via '.($pr->account->title ?? 'Unknown'),
                'amount' => $pr->amount,
                'color' => 'primary',
            ]);
        }

        // Sale Deliveries
        $deliveries = SaleDelivery::with(['sale', 'product'])->whereBetween('date', [$from, $to])->get();
        foreach ($deliveries as $d) {
            $activities->push([
                'date' => $d->date,
                'created_at' => $d->created_at,
                'type' => 'Delivery',
                'ref' => $d->refID,
                'description' => 'Delivered '.$d->qty.'x '.($d->product->name ?? 'Product').' for Sale #'.($d->sale->refID ?? ''),
                'amount' => 0,
                'color' => 'info',
            ]);
        }

        // Sort by date then created_at
        $sortedActivities = $activities->sortByDesc(function ($item) {
            return $item['date'].' '.$item['created_at'];
        })->values();

        return view('reports.activity.details', compact('sortedActivities', 'from', 'to'));
    }
}
