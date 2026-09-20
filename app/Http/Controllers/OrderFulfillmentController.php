<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderFulfillmentController extends Controller
{
    public function create()
    {
        $orders = \App\Models\Order::whereIn('status', ['Pending', 'Partial'])->get();
        return view('fulfillments.create', compact('orders'));
    }

    public function getorder($id)
    {
        $order = \App\Models\Order::with('details.product')->find($id);
        
        $details = [];
        foreach ($order->details as $item) {
            $fulfilled_qty = \App\Models\OrderFulfillmentDetail::whereHas('fulfillment', function($q) use ($id) {
                $q->where('order_id', $id);
            })->where('product_id', $item->product_id)->sum('qty');
            
            $details[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'qty' => $item->qty,
                'fulfilled_qty' => $fulfilled_qty,
            ];
        }

        return response()->json(['details' => $details]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'date' => 'required',
            'fulfill_qty' => 'required|array',
        ]);

        $fulfillment = \App\Models\OrderFulfillment::create([
            'order_id' => $request->order_id,
            'date' => $request->date,
        ]);

        $ref = getRef();

        $allFulfilled = true;
        $order = \App\Models\Order::find($request->order_id);

        foreach ($request->fulfill_qty as $product_id => $qty) {
            if ($qty > 0) {
                \App\Models\OrderFulfillmentDetail::create([
                    'order_fulfillment_id' => $fulfillment->id,
                    'product_id' => $product_id,
                    'qty' => $qty,
                ]);

                // Deduct from stock
                createStock($product_id, 'App\Models\products', 0, $qty, $request->date, 'Order Fulfillment #' . $fulfillment->id, $ref);
            }

            $ordered_qty = $order->details->where('product_id', $product_id)->first()->qty;
            
            $fulfilled_qty = \App\Models\OrderFulfillmentDetail::whereHas('fulfillment', function($q) use ($order) {
                $q->where('order_id', $order->id);
            })->where('product_id', $product_id)->sum('qty');

            if ($fulfilled_qty < $ordered_qty) {
                $allFulfilled = false;
            }
        }

        if ($allFulfilled) {
            $order->update(['status' => 'Fulfilled']);
        } else {
            $order->update(['status' => 'Partial']);
        }

        return redirect()->back()->with('success', 'Order Fulfilled Successfully');
    }
}
