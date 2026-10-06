<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->type ?? 'Ready Made Stock';

        if ($type == 'Ready Made Stock') {
            $products = products::active()->ready()->get();
            $type = 'Ready Made';
        } else {
            $products = products::active()->rawMaterial()->get();
            $type = 'Raw Material';
        }

        return view('product_mgmt.stock', compact('products', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $from = $request->from;
        $to = $request->to;

        $product = products::find($id);

        $pre_stocks = stock::where('item_id', $id)->where('date', '<', $from)->get();
        $pre_balance = 0;
        foreach ($pre_stocks as $item) {
            $pre_balance += $item->cr;
            $pre_balance -= $item->db;
        }

        $stocks = stock::where('item_id', $id)->whereBetween('date', [$from, $to])->get();

        return view('product_mgmt.stock_details', compact('product', 'stocks', 'pre_balance', 'from', 'to'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(stock $stock)
    {
        //
    }
}
