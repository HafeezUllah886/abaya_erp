<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReceiveVoucherController extends Controller
{
    public function create()
    {
        $products = \App\Models\products::all();
        $tailors = \App\Models\account::where('type', 'Supplier')->get();
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

        $voucher = \App\Models\ReceiveVoucher::create([
            'tailor_id' => $request->tailor_id,
            'date' => $request->date,
            'status' => 'Received',
        ]);

        $ref = getRef();

        foreach ($request->id as $key => $product_id) {
            $qty = $request->qty[$key];

            \App\Models\ReceiveVoucherDetail::create([
                'receive_voucher_id' => $voucher->id,
                'product_id' => $product_id,
                'qty' => $qty,
                'cost' => 0, // In full implementation, link to IssueVoucher and compute cost
            ]);

            createStock($product_id, 'App\Models\products', $qty, 0, $request->date, 'Received from tailor voucher #' . $voucher->id, $ref);
        }

        return redirect()->back()->with('success', 'Receive Voucher Created Successfully');
    }

    public function getproduct($id)
    {
        return \App\Models\products::find($id);
    }
