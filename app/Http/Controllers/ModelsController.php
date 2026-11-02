<?php

namespace App\Http\Controllers;

use App\Models\models;
use App\Models\products;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModelsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = products::active()->ready()->get();
        $models = models::with('product')->orderBy('product_id', 'asc')->get();

        return view('product_mgmt.models', compact('products', 'models'));
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
    public function store(Request $request): RedirectResponse
    {
        if (! $request->has('product_id') && $request->has('product')) {
            $request->merge(['product_id' => $request->product]);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'model_number' => 'required|string|max:255',
        ]);

        models::create([
            'product_id' => $request->product_id,
            'model_number' => $request->model_number,
        ]);

        return back()->with('success', 'Model Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(models $models)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(models $models)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, models $model): RedirectResponse
    {
        if (! $request->has('product_id') && $request->has('product')) {
            $request->merge(['product_id' => $request->product]);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'model_number' => 'required|string|max:255',
        ]);

        $model->update([
            'product_id' => $request->product_id,
            'model_number' => $request->model_number,
        ]);

        return back()->with('success', 'Model Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(models $model): RedirectResponse
    {
        $model->delete();

        return back()->with('success', 'Model Deleted Successfully');
    }
}
