<?php

use App\Models\accounts;
use App\Models\products;
use App\Models\sale;
use App\Models\sale_details;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = accounts::create([
        'title' => 'Test Customer',
        'type' => 'Customer',
        'is_active' => true,
    ]);
    $this->account = accounts::create([
        'title' => 'Cash Account',
        'type' => 'Business',
        'is_active' => true,
    ]);
    $this->product = products::create([
        'name' => 'Royal Abaya',
        'type' => 'Ready Made',
        'price' => 200.00,
        'unit' => 'Pcs',
        'is_active' => true,
    ]);
});

test('create sale view displays model column and input', function () {
    $response = $this->actingAs($this->user)->get(route('customer-sales.create'));

    $response->assertStatus(200);
    $response->assertSee('<th class="p-1">Model</th>', false);
    $response->assertSee('name="model[]"', false);
});

test('storing sale saves model into sale_details', function () {
    $response = $this->actingAs($this->user)->post(route('customer-sales.store'), [
        'bill_number' => 5001,
        'customer_id' => $this->customer->id,
        'sale_type' => 'order',
        'date' => now()->toDateString(),
        'delivery_date' => now()->addDays(7)->toDateString(),
        'total_bill' => 400,
        'id' => [$this->product->id],
        'model' => ['MOD-2026-X'],
        'price' => [200],
        'qty' => [2],
        'delivered_qty' => [0],
        'amount' => [400],
        'account_id' => [$this->account->id],
        'payment_amount' => [0],
        'payment_notes' => [''],
    ]);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('sale_details', [
        'product_id' => $this->product->id,
        'model' => 'MOD-2026-X',
        'qty' => 2,
        'price' => 200,
    ]);
});

test('sale view and edit pages display model', function () {
    $sale = sale::create([
        'bill_number' => 5002,
        'customer_id' => $this->customer->id,
        'date' => now()->toDateString(),
        'sale_type' => 'order',
        'total' => 200,
        'total_bill' => 200,
        'refID' => 998877,
    ]);

    $detail = sale_details::create([
        'sale_id' => $sale->id,
        'product_id' => $this->product->id,
        'model' => 'ABAYA-LUX-01',
        'price' => 200,
        'qty' => 1,
        'delivered_qty' => 0,
        'amount' => 200,
        'date' => now()->toDateString(),
        'refID' => 998877,
    ]);

    $viewResponse = $this->actingAs($this->user)->get(route('customer-sales.show', $sale->id));
    $viewResponse->assertStatus(200);
    $viewResponse->assertSee('ABAYA-LUX-01');

    $editResponse = $this->actingAs($this->user)->get(route('customer-sales.edit', $sale->id));
    $editResponse->assertStatus(200);
    $editResponse->assertSee('ABAYA-LUX-01');
    $editResponse->assertSee('name="model[]"', false);
});
