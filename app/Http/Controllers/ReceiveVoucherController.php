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
        $products = products::all();
        $tailors = accounts::where('type', 'Supplier')->get();

        return view('manufacturing.receive.create', compact('products', 'tailors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tailor_id' => 'required',
            'date' => 'required',
            'id' => 'required|array',
            'qty' => 'required|array',
        ]);

        $voucher = ReceiveVoucher::create([
            'tailor_id' => $request->tailor_id,
            'date' => $request->date,
            'status' => 'Received',
        ]);

        $ref = getRef();

        foreach ($request->id as $key => $product_id) {
            $qty = $request->qty[$key];

            ReceiveVoucherDetail::create([
                'receive_voucher_id' => $voucher->id,
                'product_id' => $product_id,
                'qty' => $qty,
                'cost' => 0, // In full implementation, link to IssueVoucher and compute cost
            ]);

            createStock($product_id, 'App\Models\products', $qty, 0, $request->date, 'Received from tailor voucher #'.$voucher->id, $ref);
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
