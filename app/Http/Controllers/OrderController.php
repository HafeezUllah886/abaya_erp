<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $products = \App\Models\products::all();
        $customers = \App\Models\accounts::where('type', 'Customer')->get();
        return view('orders.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'date' => 'required',
            'id' => 'required|array',
            'qty' => 'required|array',
            'price' => 'required|array',
        ]);

        $order = \App\Models\Order::create([
            'customer_id' => $request->customer_id,
            'date' => $request->date,
            'status' => 'Pending',
            'total' => 0,
        ]);

        $total = 0;

        foreach ($request->id as $key => $product_id) {
            $qty = $request->qty[$key];
            $price = $request->price[$key];
            $amount = $qty * $price;
            $total += $amount;

            \App\Models\OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product_id,
                'qty' => $qty,
                'price' => $price,
                'amount' => $amount,
            ]);
        }

        $order->update(['total' => $total]);

        return redirect()->back()->with('success', 'Order Created Successfully');
    }

    public function getproduct($id)
    {
        return \App\Models\products::find($id);
    }
}
