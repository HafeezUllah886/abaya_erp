<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ConfirmPassword;
use App\Models\accounts;
use App\Models\products;
use App\Models\sale;
use App\Models\sale_details;
use App\Models\SaleDelivery;
use App\Models\salePayments;
use App\Models\stock;
use App\Models\transactions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /*  public function __construct()
     {
         // Apply middleware to the edit method
         $this->middleware(ConfirmPassword::class)->only('edit');
     } */

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? lastDayOfMonth();
        $customer = $request->customer ?? 'all';
        $status = $request->status ?? 'all';

        $sales = sale::whereBetween('date', [$from, $to])
            ->when($customer != 'all', function ($query) use ($customer) {
                $query->where('customer_id', $customer);
            })
            ->orderby('id', 'desc')
            ->get();

        if ($status != 'all') {
            $sales = $sales->filter(function ($sale) use ($status) {
                $total_qty = $sale->details->sum('qty');
                $total_delivered = $sale->details->sum('delivered_qty');
                $delivery_status = 'pending';
                if ($total_delivered >= $total_qty && $total_qty > 0) {
                    $delivery_status = 'delivered';
                } elseif ($total_delivered > 0) {
                    $delivery_status = 'partial';
                }

                return $delivery_status == $status;
            });
        }

        $customers = accounts::active()->customer()->get();

        return view('sale.index', compact('sales', 'from', 'to', 'customer', 'status', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::active()->ready()->get();
        $customers = accounts::active()->customer()->get();
        $accounts = accounts::active()->business()->get();

        return view('sale.create', compact('products', 'customers', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            if ($request->isNotFilled('id')) {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            $ref = getRef();
            $sale = sale::create(
                [
                    'customer_id' => $request->customer_id,
                    'customer_name' => $request->customer_name,
                    'contact' => $request->contact,
                    'date' => $request->date,
                    'delivery_date' => $request->delivery_date,
                    'notes' => $request->notes,
                    'vat' => $request->vat,
                    'vat_amount' => $request->vat_amount,
                    'total_bill' => $request->total_bill,
                    'refID' => $ref,
                ]
            );

            $ids = $request->id;
            $total = 0;
            foreach ($ids as $key => $id) {
                if ($request->qty[$key] > 0) {
                    $qty = $request->qty[$key];
                    $price = $request->price[$key];
                    $amount = $request->amount[$key];
                    $delivered_qty = $request->delivered_qty[$key] ?? 0;
                    $total += $amount;

                    sale_details::create(
                        [
                            'sale_id' => $sale->id,
                            'product_id' => $id,
                            'price' => $price,
                            'qty' => $qty,
                            'delivered_qty' => $delivered_qty,
                            'amount' => $amount,
                            'date' => $request->date,
                            'refID' => $ref,
                        ]
                    );

                    if ($delivered_qty > 0) {
                        SaleDelivery::create([
                            'sale_id' => $sale->id,
                            'product_id' => $id,
                            'qty' => $delivered_qty,
                            'date' => $request->date,
                            'refID' => $ref,
                        ]);
                        createStock($id, 0, $delivered_qty, $request->date, "Delivered in Sale # $sale->id", $ref);
                    }
                }
            }
            $sale->update(
                [
                    'total' => $total,
                ]
            );

            $account_ids = $request->account_id;
            $payment_amount = $request->payment_amount;
            $payment_notes = $request->payment_notes;
            foreach ($account_ids as $key => $account_id) {
                if ($payment_amount[$key] > 0) {
                    $account = accounts::find($account_id);
                    createTransaction($account->id, $request->date, $payment_amount[$key], 0, "Payment of Sale # $sale->id Remarks".$payment_notes[$key], $ref);

                    salePayments::create(
                        [
                            'sale_id' => $sale->id,
                            'account_id' => $account_id,
                            'amount' => $payment_amount[$key],
                            'notes' => $payment_notes[$key],
                            'date' => $request->date,
                            'refID' => $ref,
                        ]
                    );
                }

            }

            DB::commit();

            return back()->with('success', 'Sale Created');

        } catch (Exception $e) {
            DB::rollback();

            return back()->with('error', $e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(sale $sale)
    {
        return view('sale.view', compact('sale'));
    }

    public function edit(sale $sale)
    {
        $products = products::active()->ready()->orderby('name', 'asc')->get();
        $customers = accounts::active()->customer()->get();
        $accounts = accounts::active()->business()->get();

        return view('sale.edit', compact('products', 'customers', 'accounts', 'sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, sale $sale)
    {
        try {
            if ($request->isNotFilled('id')) {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            foreach ($sale->details as $product) {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }

            foreach ($sale->payments as $payment) {
                $payment->delete();
            }
            SaleDelivery::where('sale_id', $sale->id)->delete();
            transactions::where('refID', $sale->refID)->delete();

            $sale->update(
                [
                    'customer_id' => $request->customer_id,
                    'customer_name' => $request->customer_name,
                    'contact' => $request->contact,
                    'date' => $request->date,
                    'delivery_date' => $request->delivery_date,
                    'notes' => $request->notes,
                    'status' => $request->status ?? 'paid',
                    'vat' => $request->vat,
                    'vat_amount' => $request->vat_amount,
                    'total_bill' => $request->total_bill,
                ]
            );

            $ids = $request->id;
            $ref = $sale->refID;

            $total = 0;
            foreach ($ids as $key => $id) {
                if ($request->qty[$key] > 0) {
                    $qty = $request->qty[$key];
                    $price = $request->price[$key];
                    $amount = $request->amount[$key];
                    $delivered_qty = $request->delivered_qty[$key] ?? 0;
                    $total += $amount;

                    sale_details::create(
                        [
                            'sale_id' => $sale->id,
                            'product_id' => $id,
                            'price' => $price,
                            'qty' => $qty,
                            'delivered_qty' => $delivered_qty,
                            'amount' => $amount,
                            'date' => $request->date,
                            'refID' => $ref,
                        ]
                    );

                    if ($delivered_qty > 0) {
                        SaleDelivery::create([
                            'sale_id' => $sale->id,
                            'product_id' => $id,
                            'qty' => $delivered_qty,
                            'date' => $request->date,
                            'refID' => $ref,
                        ]);
                        createStock($id, 0, $delivered_qty, $request->date, "Delivered in Sale # $sale->id", $ref);
                    }
                }
            }

            $sale->update(
                [
                    'total' => $total,
                ]
            );

            if ($request->status == 'paid') {
                $account_ids = $request->account_id;
                $payment_amount = $request->payment_amount;
                $payment_notes = $request->payment_notes;
                foreach ($account_ids as $key => $account_id) {
                    if ($payment_amount[$key] > 0) {
                        $account = accounts::find($account_id);
                        createTransaction($account->id, $request->date, $payment_amount[$key], 0, "Payment of Sale # $sale->id Remarks".$payment_notes[$key], $ref);

                        salePayments::create(
                            [
                                'sale_id' => $sale->id,
                                'account_id' => $account_id,
                                'amount' => $payment_amount[$key],
                                'notes' => $payment_notes[$key],
                                'date' => $request->date,
                                'refID' => $ref,
                            ]
                        );
                    }

                }
            } else {
                createTransaction($request->customer_id, $request->date, $total, 0, "Pending Amount of Sale # $sale->id", $ref);
            }
            DB::commit();
            session()->forget('confirmed_password');

            return to_route('sale.index')->with('success', 'Sale Updated');
        } catch (Exception $e) {
            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();
            $sale = sale::find($id);

            foreach ($sale->details as $product) {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            foreach ($sale->payments as $payment) {
                $payment->delete();
            }
            SaleDelivery::where('sale_id', $sale->id)->delete();
            transactions::where('refID', $sale->refID)->delete();
            $sale->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return redirect()->route('sale.index')->with('success', 'Sale Deleted');
        } catch (Exception $e) {
            DB::rollBack();
            session()->forget('confirmed_password');

            return redirect()->route('sale.index')->with('error', $e->getMessage());
        }
    }

    public function getSignleProduct($id)
    {
        $product = products::find($id);

        return $product;
    }

    public function deliver($id)
    {
        $sale = sale::with('details.product', 'payments')->find($id);
        $accounts = accounts::active()->business()->get();
        $paid_amount = $sale->payments->sum('amount');
        $balance = $sale->total_bill - $paid_amount;

        return view('sale.deliver', compact('sale', 'accounts', 'balance'));
    }

    public function storeDelivery(Request $request, $id)
    {
        $sale = sale::find($id);

        // Handle delivery
        $deliver_now = $request->input('deliver_now', []);
        if (is_array($deliver_now) && count($deliver_now) > 0) {
            $ref = getRef();
            foreach ($deliver_now as $product_id => $qty) {
                if ($qty > 0) {
                    $saleDetail = $sale->details()->where('product_id', $product_id)->first();
                    if ($saleDetail) {
                        $new_delivered_qty = $saleDetail->delivered_qty + $qty;
                        $saleDetail->update(['delivered_qty' => $new_delivered_qty]);

                        SaleDelivery::create([
                            'sale_id' => $sale->id,
                            'product_id' => $product_id,
                            'qty' => $qty,
                            'date' => $request->date,
                            'refID' => $ref,
                        ]);

                        // Deduct from stock
                        createStock($product_id, 0, $qty, $request->date, "Delivered in Sale # $sale->id".($request->notes ? ' Notes: '.$request->notes : ''), $ref);
                    }
                }
            }
        }

        // Handle payments
        if ($request->has('account_id') && $request->has('payment_amount')) {
            $ref = getRef();
            $account_ids = $request->account_id;
            $payment_amount = $request->payment_amount;
            $payment_notes = $request->payment_notes;
            foreach ($account_ids as $key => $account_id) {
                if ($payment_amount[$key] > 0) {
                    $account = accounts::find($account_id);
                    createTransaction($account->id, $request->date, $payment_amount[$key], 0, "Payment of Sale # $sale->id Remarks: ".$payment_notes[$key], $ref);

                    salePayments::create(
                        [
                            'sale_id' => $sale->id,
                            'account_id' => $account_id,
                            'amount' => $payment_amount[$key],
                            'notes' => $payment_notes[$key],
                            'date' => $request->date,
                            'refID' => $ref,
                        ]
                    );
                }
            }
        }

        return redirect()->route('sale.index')->with('success', 'Delivery and payment updated successfully.');
    }
}
