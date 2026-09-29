<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\products;
use App\Models\ReceiveVoucher;
use App\Models\ReceiveVoucherDetail;
use Illuminate\Http\Request;

class ReceiveVoucherController extends Controller
{
    public function create()
    {
        $products = products::finished()->get();
        $tailors = accounts::tailor()->get();
        $accounts = accounts::business()->get();

        return view('manufacturing.receive.create', compact('products', 'tailors', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tailor_id' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric|min:0',
            'payment_status' => 'required',
            'id' => 'required|array',
            'qty' => 'required|array',
        ]);

        if ($request->payment_status == 'Paid') {
            $request->validate([
                'account_id' => 'required',
            ]);
        }

        $ref = getRef();

        $voucher = ReceiveVoucher::create([
            'tailor_id' => $request->tailor_id,
            'date' => $request->date,
            'status' => 'Received',
            'refID' => $ref,
            'stitching_charges_total' => $request->amount,
        ]);

        foreach ($request->id as $key => $product_id) {
            $qty = $request->qty[$key];

            ReceiveVoucherDetail::create([
                'receive_voucher_id' => $voucher->id,
                'product_id' => $product_id,
                'qty' => $qty,
                'stitching_cost_per_unit' => 0,
                'total_calculated_cost' => 0,
            ]);

            createStock($product_id, 'App\Models\products', $qty, 0, $request->date, 'Received from tailor voucher #'.$voucher->id, $ref);
        }

        if ($request->payment_status == 'Paid') {
            createTransaction($request->account_id, $request->date, 0, $request->amount, 'Payment for Receive Voucher #'.$voucher->id, $ref);
            createTransaction($request->tailor_id, $request->date, $request->amount, $request->amount, 'Payment for Receive Voucher #'.$voucher->id, $ref);
        } else {
            createTransaction($request->tailor_id, $request->date, 0, $request->amount, 'Pending Amount for Receive Voucher #'.$voucher->id, $ref);
        }

        return redirect()->back()->with('success', 'Receive Voucher Created Successfully');
    }

    public function getproduct($id)
    {
        return products::find($id);
    }

    public function index(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? lastDayOfMonth();
        $tailor = $request->tailor ?? 'all';

        $vouchers = ReceiveVoucher::with('tailor')
            ->whereBetween('date', [$from, $to])
            ->when($tailor != 'all', function ($query) use ($tailor) {
                $query->where('tailor_id', $tailor);
            })
            ->orderby('id', 'desc')
            ->get();

        $tailors = accounts::where('type', 'Supplier')->get();

        return view('manufacturing.receive.index', compact('vouchers', 'from', 'to', 'tailor', 'tailors'));
    }

    public function show(ReceiveVoucher $receiveVoucher)
    {
        return view('manufacturing.receive.show', ['voucher' => $receiveVoucher]);
    }

    public function edit(ReceiveVoucher $receiveVoucher)
    {
        $voucher = $receiveVoucher;
        $products = products::all();
        $tailors = accounts::where('type', 'Supplier')->get();

        return view('manufacturing.receive.edit', compact('voucher', 'products', 'tailors'));
    }

    public function update(Request $request, ReceiveVoucher $receiveVoucher)
    {
        $receiveVoucher->update([
            'tailor_id' => $request->tailor_id,
            'date' => $request->date,
        ]);

        return redirect()->route('receive_vouchers.index')->with('success', 'Voucher Updated Successfully');
    }

    public function destroy(ReceiveVoucher $receiveVoucher)
    {
        foreach ($receiveVoucher->details as $detail) {
            $detail->delete();
        }
        $receiveVoucher->delete();

        return redirect()->back()->with('success', 'Receive Voucher Deleted Successfully');
    }
}
