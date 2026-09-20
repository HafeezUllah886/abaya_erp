<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IssueVoucherController extends Controller
{
    public function create()
    {
        $raw_materials = \App\Models\RawMaterial::all();
        $tailors = \App\Models\account::where('type', 'Supplier')->get();
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

        foreach ($request->id as $key => $raw_material_id) {
            $qty = $request->qty[$key];

            \App\Models\IssueVoucherDetail::create([
                'issue_voucher_id' => $voucher->id,
                'raw_material_id' => $raw_material_id,
                'qty' => $qty,
            ]);

            createStock($raw_material_id, 'App\Models\RawMaterial', 0, $qty, $request->date, 'Issued to tailor voucher #' . $voucher->id, $ref);
        }

        return redirect()->back()->with('success', 'Issue Voucher Created Successfully');
    }

    public function getproduct($id)
    {
        return \App\Models\RawMaterial::find($id);
    }
}
