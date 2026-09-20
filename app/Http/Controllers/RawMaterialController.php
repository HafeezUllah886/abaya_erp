<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function index()
    {
        $items = RawMaterial::all();

        return view('product_mgmt.raw_materials', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'unit' => 'required',
        ]);

        RawMaterial::create($request->all());

        return back()->with('success', 'Raw Material Created');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'unit' => 'required',
        ]);

        $item = RawMaterial::find($id);
        $item->update($request->all());

        return back()->with('success', 'Raw Material Updated');
    }
}
