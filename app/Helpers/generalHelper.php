<?php

use App\Models\products;
use App\Models\purchase_details;
use App\Models\ref;
use App\Models\sale_details;
use App\Models\stock;
use Carbon\Carbon;

function getRef()
{
    $ref = ref::first();
    if ($ref) {
        $ref->ref = $ref->ref + 1;
    } else {
        $ref = new ref;
        $ref->ref = 1;
    }
    $ref->save();

    return $ref->ref;
}

function firstDayOfMonth()
{
    $startOfMonth = Carbon::now()->startOfMonth();

    return $startOfMonth->format('Y-m-d');
}
function lastDayOfMonth()
{

    $endOfMonth = Carbon::now()->endOfMonth();

    return $endOfMonth->format('Y-m-d');
}

function createStock($id, $type, $cr, $db, $date, $notes, $ref)
{
    stock::create(
        [
            'item_id' => $id,
            'item_type' => $type,
            'cr' => $cr,
            'db' => $db,
            'date' => $date,
            'notes' => $notes,
            'refID' => $ref,
        ]
    );
}
function getStock($id, $type)
{
    if ($type == 'finished') {
        $type = 'App\Models\products';
    } else {
        $type = 'App\Models\products';
    }
    $stocks = stock::where('item_id', $id)->where('item_type', $type)->get();
    $balance = 0;
    foreach ($stocks as $stock) {
        $balance += $stock->cr;
        $balance -= $stock->db;
    }

    return $balance;
}

function getStockOnTime($id, $type, $time)
{
    $stocks = stock::where('item_id', $id)->where('item_type', $type)->whereDate('created_at', '<=', $time)->get();
    $balance = 0;
    foreach ($stocks as $stock) {
        $balance += $stock->cr;
        $balance -= $stock->db;
    }

    return $balance;
}

function avgSalePrice($from, $to, $id)
{
    $sales = sale_details::where('product_id', $id);
    if ($from != 'all' && $to != 'all') {
        $sales->whereBetween('date', [$from, $to]);
    }
    $sales_amount = $sales->sum('amount');
    $sales_qty = $sales->sum('qty');

    if ($sales_qty > 0) {
        $sale_price = $sales_amount / $sales_qty;
    } else {
        $sale_price = 0;
    }

    return $sale_price;
}

function avgPurchasePrice($from, $to, $id)
{
    $purchases = purchase_details::where('product_id', $id);
    if ($from != 'all' && $to != 'all') {
        $purchases->whereBetween('date', [$from, $to]);
    }
    $purchase_amount = $purchases->sum('amount');
    $purchase_qty = $purchases->sum('qty');

    if ($purchase_qty > 0) {
        $purchase_price = $purchase_amount / $purchase_qty;
    } else {
        $purchase_price = 0;
    }

    return $purchase_price;
}

function stockValue()
{
    $rawMaterials = products::RawMaterial()->get();

    $value = 0;
    foreach ($rawMaterials as $item) {
        $value += itemStockValue($item->id, 'Raw Material');
    }

    $products = products::Finished()->get();
    foreach ($products as $item) {
        $value += itemStockValue($item->id, 'Finished Abaya');
    }

    return $value;
}

function itemStockValue($id, $type)
{
    $stock = getStock($id, 'App\Models\products');

    $price = 0;
    if ($type === 'Raw Material') {
        $price = avgPurchasePrice('all', 'all', $id);
    } elseif ($type === 'Finished Abaya') {
        $price = avgManufacturingCost('all', 'all', $id);
    }

    return $price * $stock;
}

function avgManufacturingCost($from, $to, $id)
{
    // Simplified: Return 0 until strict costing in ReceiveVouchers is built.
    return 0;
}

function projectName()
{
    return 'ABAYA ERP';
}

function projectNameShort()
{
    return 'ABAYA';
}

function addressLineOne()
{
    return 'ABC Road';
}

function addressLineTwo()
{
    return 'Quetta';
}
