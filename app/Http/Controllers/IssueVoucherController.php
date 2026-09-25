<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IssueVoucherController extends Controller
{
    public function create()
    {
        $raw_materials = \App\Models\products::active()->rawMaterial()->get();
        $tailors = \App\Models\accounts::where('type', 'Supplier')->get();
        return view('manufacturing.issue.create', compact('raw_materials', 'tailors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tailor_id' => 'required',
            'date' => 'required',
            'id' => 'required|array',
            'qty' => 'required|array',
        ]);

        $voucher = \App\Models\IssueVoucher::create([
            'tailor_id' => $request->tailor_id,
            'date' => $request->date,
            'status' => 'Issued',
        ]);

        $ref = getRef();

        foreach ($request->id as $key => $product_id) {
            $qty = $request->qty[$key];

            \App\Models\IssueVoucherDetail::create([
                'issue_voucher_id' => $voucher->id,
                'product_id' => $product_id,
                'qty' => $qty,
            ]);

            createStock($product_id, 'App\Models\products', 0, $qty, $request->date, 'Issued to tailor voucher #' . $voucher->id, $ref);
        }

        return redirect()->back()->with('success', 'Issue Voucher Created Successfully');
    }

    public function getproduct($id)
    {
        return \App\Models\products::find($id);
    }
}
