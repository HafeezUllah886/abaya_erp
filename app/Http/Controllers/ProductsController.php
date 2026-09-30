<?php

namespace App\Http\Controllers;

use App\Models\products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->type ?? 'Finished Abaya';

        if ($type == 'Finished Abaya') {
            $items = products::active()->finished()->get();
        } else {
            $items = products::active()->rawMaterial()->get();
        }

        return view('product_mgmt.products', compact('items', 'type'));
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
        $request->validate(
            [
                'name' => 'required',
                'price' => 'required|numeric',
            ]
        );

        products::create($request->all());

        return back()->with('success', 'Product Created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'required',
                'price' => 'required|numeric',
            ]
        );

        $product = products::find($id);
        $product->update($request->all());

        return back()->with('success', 'Product Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(products $products)
    {
        //
    }

    public function ajaxCreate(Request $request)
    {
        $check = products::where('name', $request->name)->count();
        if ($check > 0) {
            return response()->json(
                ['response' => 'Exists']
            );
        }
        $product = products::create($request->all());

        return response()->json(
            ['response' => $product->id]
        );
    }
}
