<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\ManufacturingOrder;
use App\Models\ManufacturingOrderMaterial;
use App\Models\ManufacturingOrderProduct;
use App\Models\products;
use App\Models\stock;
use App\Models\transactions;
use Illuminate\Http\Request;

class ManufacturingOrderController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? lastDayOfMonth();
        $tailor = $request->tailor ?? 'all';

        $orders = ManufacturingOrder::with('tailor')
            ->whereBetween('date', [$from, $to])
            ->when($tailor != 'all', function ($query) use ($tailor) {
                $query->where('tailor_id', $tailor);
            })
            ->orderby('id', 'desc')
            ->get();

        $tailors = accounts::tailor()->get();

        return view('manufacturing.orders.index', compact('orders', 'from', 'to', 'tailor', 'tailors'));
    }

    public function create()
    {
        $raw_materials = products::rawMaterial()->active()->get();
        $finished_products = products::finished()->active()->get();
        $tailors = accounts::tailor()->get();
        $business_accounts = accounts::business()->get();

        return view('manufacturing.orders.create', compact('raw_materials', 'finished_products', 'tailors', 'business_accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tailor_id' => 'required',
            'date' => 'required|date',
            'payment_type' => 'required|in:Paid,Unpaid',
            'total_amount' => 'required|numeric|min:0',
        ]);

        if ($request->payment_type == 'Paid') {
            $request->validate([
                'business_account_id' => 'required',
            ]);
        }

        $ref = getRef();

        $order = ManufacturingOrder::create([
            'date' => $request->date,
            'tailor_id' => $request->tailor_id,
            'payment_type' => $request->payment_type,
            'business_account_id' => $request->payment_type == 'Paid' ? $request->business_account_id : null,
            'total_amount' => $request->total_amount,
            'notes' => $request->notes,
            'refID' => $ref,
        ]);

        // Process Finished Products
        if ($request->has('product_id')) {
            foreach ($request->product_id as $key => $product_id) {
                $order_qty = $request->order_qty[$key] ?? 0;
                $received_qty = $request->received_qty[$key] ?? 0;

                if ($order_qty > 0 || $received_qty > 0) {
                    ManufacturingOrderProduct::create([
                        'manufacturing_order_id' => $order->id,
                        'product_id' => $product_id,
                        'order_qty' => $order_qty,
                        'received_qty' => $received_qty,
                    ]);

                    if ($received_qty > 0) {
                        createStock($product_id, 'App\Models\products', $received_qty, 0, $request->date, 'Received from tailor for Mfg Order #'.$order->id, $ref);
                    }
                }
            }
        }

        // Process Raw Materials
        if ($request->has('material_id')) {
            foreach ($request->material_id as $key => $material_id) {
                $own_qty = $request->own_qty[$key] ?? 0;
                $customer_qty = $request->customer_qty[$key] ?? 0;

                if ($own_qty > 0 || $customer_qty > 0) {
                    $cost_price = $request->cost_price[$key] ?? avgPurchasePrice('all', 'all', $material_id);

                    ManufacturingOrderMaterial::create([
                        'manufacturing_order_id' => $order->id,
                        'product_id' => $material_id,
                        'own_qty' => $own_qty,
                        'customer_qty' => $customer_qty,
                        'cost_price' => $cost_price,
                    ]);

                    if ($own_qty > 0) {
                        createStock($material_id, 'App\Models\products', 0, $own_qty, $request->date, 'Issued to tailor for Mfg Order #'.$order->id, $ref);
                    }
                }
            }
        }

        // Process Payment
        if ($request->total_amount > 0) {
            if ($request->payment_type == 'Paid') {
                createTransaction($request->business_account_id, $request->date, 0, $request->total_amount, 'Payment for Mfg Order #'.$order->id, $ref);
                createTransaction($request->tailor_id, $request->date, $request->total_amount, $request->total_amount, 'Payment for Mfg Order #'.$order->id, $ref);
            } else {
                createTransaction($request->tailor_id, $request->date, 0, $request->total_amount, 'Pending Amount for Mfg Order #'.$order->id, $ref);
            }
        }

        return redirect()->route('manufacturing_orders.index')->with('success', 'Manufacturing Order Created Successfully');
    }

    public function show(ManufacturingOrder $order)
    {
        return view('manufacturing.orders.show', compact('order'));
    }

    public function edit(ManufacturingOrder $order)
    {
        $raw_materials = products::active()->rawMaterial()->get();
        $finished_products = products::active()->finished()->get();
        $tailors = accounts::tailor()->get();
        $business_accounts = accounts::business()->get();

        return view('manufacturing.orders.edit', compact('order', 'raw_materials', 'finished_products', 'tailors', 'business_accounts'));
    }

    public function update(Request $request, ManufacturingOrder $order)
    {
        $request->validate([
            'tailor_id' => 'required',
            'date' => 'required|date',
            'payment_type' => 'required|in:Paid,Unpaid',
            'total_amount' => 'required|numeric|min:0',
        ]);

        if ($request->payment_type == 'Paid') {
            $request->validate([
                'business_account_id' => 'required',
            ]);
        }

        // Delete previous related records
        transactions::where('refID', $order->refID)->delete();
        stock::where('refID', $order->refID)->delete();
        $order->products()->delete();
        $order->materials()->delete();

        // Update the main order
        $order->update([
            'date' => $request->date,
            'tailor_id' => $request->tailor_id,
            'payment_type' => $request->payment_type,
            'business_account_id' => $request->payment_type == 'Paid' ? $request->business_account_id : null,
            'total_amount' => $request->total_amount,
            'notes' => $request->notes,
        ]);

        $ref = $order->refID;

        // Process Finished Products
        if ($request->has('product_id')) {
            foreach ($request->product_id as $key => $product_id) {
                $order_qty = $request->order_qty[$key] ?? 0;
                $received_qty = $request->received_qty[$key] ?? 0;

                if ($order_qty > 0 || $received_qty > 0) {
                    ManufacturingOrderProduct::create([
                        'manufacturing_order_id' => $order->id,
                        'product_id' => $product_id,
                        'order_qty' => $order_qty,
                        'received_qty' => $received_qty,
                    ]);

                    if ($received_qty > 0) {
                        createStock($product_id, $received_qty, 0, $request->date, 'Received from tailor for Mfg Order #'.$order->id, $ref);
                    }
                }
            }
        }

        // Process Raw Materials
        if ($request->has('material_id')) {
            foreach ($request->material_id as $key => $material_id) {
                $own_qty = $request->own_qty[$key] ?? 0;
                $customer_qty = $request->customer_qty[$key] ?? 0;

                if ($own_qty > 0 || $customer_qty > 0) {
                    $cost_price = $request->cost_price[$key] ?? avgPurchasePrice('all', 'all', $material_id);

                    ManufacturingOrderMaterial::create([
                        'manufacturing_order_id' => $order->id,
                        'product_id' => $material_id,
                        'own_qty' => $own_qty,
                        'customer_qty' => $customer_qty,
                        'cost_price' => $cost_price,
                    ]);

                    if ($own_qty > 0) {
                        createStock($material_id, 0, $own_qty, $request->date, 'Issued to tailor for Mfg Order #'.$order->id, $ref);
                    }
                }
            }
        }

        // Process Payment
        if ($request->total_amount > 0) {
            if ($request->payment_type == 'Paid') {
                createTransaction($request->business_account_id, $request->date, 0, $request->total_amount, 'Payment for Mfg Order #'.$order->id, $ref);
                createTransaction($request->tailor_id, $request->date, $request->total_amount, $request->total_amount, 'Payment for Mfg Order #'.$order->id, $ref);
            } else {
                createTransaction($request->tailor_id, $request->date, 0, $request->total_amount, 'Pending Amount for Mfg Order #'.$order->id, $ref);
            }
        }

        return redirect()->route('manufacturing_orders.index')->with('success', 'Manufacturing Order Updated Successfully');
    }

    public function destroy(ManufacturingOrder $order)
    {
        transactions::where('refID', $order->refID)->delete();
        stock::where('refID', $order->refID)->delete();
        $order->products()->delete();
        $order->materials()->delete();
        $order->delete();

        return redirect()->route('manufacturing_orders.index')->with('success', 'Manufacturing Order Deleted Successfully');
    }
}
