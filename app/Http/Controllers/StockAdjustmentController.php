<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\RawMaterial;
use App\Models\stock;
use App\Models\stockAdjustments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adjustments = stockAdjustments::orderBy('id', 'desc')->get();
        $products = products::active()->get();
        $rawMaterials = RawMaterial::active()->get();

        return view('product_mgmt.adjustments', compact('adjustments', 'products', 'rawMaterials'));
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
        try {
            DB::beginTransaction();
            $ref = getRef();
            stockAdjustments::create(
                [
                    'item_id' => $request->item_id,
                    'item_type' => $request->item_type,
                    'user_id' => auth()->user()->id,
                    'type' => $request->type,
                    'qty' => $request->qty,
                    'date' => $request->date,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );

            if ($request->type == 'Credit') {
                createStock($request->item_id, $request->item_type, $request->qty, 0, $request->date, 'Stock Adjusted notes - '.$request->notes, $ref);
            } else {
                createStock($request->item_id, $request->item_type, 0, $request->qty, $request->date, 'Stock Adjusted notes - '.$request->notes, $ref);
            }

            DB::commit();

            return back()->with('success', 'Stock Adjusted');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $receiving = paymentReceiving::find($id);

        return view('finance.receiving.receipt', compact('receiving'));
    }

    public function edit(paymentReceiving $paymentReceiving)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, paymentReceiving $paymentReceiving)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ref)
    {

        try {
            DB::beginTransaction();
            stockAdjustments::where('refID', $ref)->delete();
            stock::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return redirect()->route('stock-adjustments.index')->with('success', 'Stock Adjustment Deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('confirmed_password');

            return redirect()->route('stock-adjustments.index')->with('error', $e->getMessage());
        }
    }
}
