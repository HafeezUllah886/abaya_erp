<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\IssueVoucher;
use App\Models\IssueVoucherDetail;
use App\Models\products;
use Illuminate\Http\Request;

class IssueVoucherController extends Controller
{
    public function create()
    {
        $raw_materials = products::active()->rawMaterial()->get();
        $tailors = accounts::where('type', 'Supplier')->get();

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

        $voucher = IssueVoucher::create([
            'tailor_id' => $request->tailor_id,
            'date' => $request->date,
            'status' => 'Issued',
        ]);

        $ref = getRef();

        foreach ($request->id as $key => $product_id) {
            $qty = $request->qty[$key];

            IssueVoucherDetail::create([
                'issue_voucher_id' => $voucher->id,
                'product_id' => $product_id,
                'qty' => $qty,
                'cost_at_issue' => avgPurchasePrice('all', 'all', $product_id),
            ]);

            createStock($product_id, 'App\Models\products', 0, $qty, $request->date, 'Issued to tailor voucher #'.$voucher->id, $ref);
        }

        return redirect()->back()->with('success', 'Issue Voucher Created Successfully');
    }

    public function getproduct($id)
    {
        return products::find($id);
    }

    public function index(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? lastDayOfMonth();
        $tailor = $request->tailor ?? 'all';
        
        $vouchers = IssueVoucher::with('tailor')
            ->whereBetween('date', [$from, $to])
            ->when($tailor != 'all', function ($query) use ($tailor) {
                $query->where('tailor_id', $tailor);
            })
            ->orderby('id', 'desc')
            ->get();

        $tailors = accounts::where('type', 'Supplier')->get();

        return view('manufacturing.issue.index', compact('vouchers', 'from', 'to', 'tailor', 'tailors'));
    }

    public function show(IssueVoucher $issueVoucher)
    {
        return view('manufacturing.issue.show', ['voucher' => $issueVoucher]);
    }

    public function edit(IssueVoucher $issueVoucher)
    {
        $voucher = $issueVoucher;
        $raw_materials = products::active()->rawMaterial()->get();
        $tailors = accounts::where('type', 'Supplier')->get();

        return view('manufacturing.issue.edit', compact('voucher', 'raw_materials', 'tailors'));
    }

    public function update(Request $request, IssueVoucher $issueVoucher)
    {
        $issueVoucher->update([
            'tailor_id' => $request->tailor_id,
            'date' => $request->date,
        ]);
        
        return redirect()->route('issue_vouchers.index')->with('success', 'Voucher Updated Successfully');
    }

    public function destroy(IssueVoucher $issueVoucher)
    {
        // Remove associated stock entries via ref note or something similar, or just basic delete
        foreach ($issueVoucher->details as $detail) {
            $detail->delete();
        }
        $issueVoucher->delete();
        return redirect()->back()->with('success', 'Issue Voucher Deleted Successfully');
    }
}
