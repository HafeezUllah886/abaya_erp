<?php

use App\Models\accounts;
use App\Models\products;
use App\Models\sale;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = accounts::create([
        'title' => 'Walk-in Customer',
        'type' => 'Customer',
        'is_active' => true,
    ]);
    $this->account = accounts::create([
        'title' => 'Cash Account',
        'type' => 'Business',
        'is_active' => true,
    ]);
    $this->product = products::create([
        'name' => 'Classic Abaya',
        'type' => 'Ready Made',
        'price' => 150.00,
        'unit' => 'Pcs',
        'is_active' => true,
    ]);
});

test('create sale view autofills bill number with 1 when no previous sale exists', function () {
    $response = $this->actingAs($this->user)->get(route('customer-sales.create'));

    $response->assertStatus(200);
    $response->assertViewHas('bill_number', 1);
    $response->assertSee('name="bill_number"', false);
    $response->assertSee('value="1"', false);
});

test('create sale view autofills bill number with increment of 1 to the previous bill', function () {
    sale::create([
        'bill_number' => 1005,
        'customer_id' => $this->customer->id,
        'date' => now()->toDateString(),
        'sale_type' => 'order',
        'total' => 150,
        'total_bill' => 150,
        'refID' => 123456,
    ]);

    $response = $this->actingAs($this->user)->get(route('customer-sales.create'));

    $response->assertStatus(200);
    $response->assertViewHas('bill_number', 1006);
    $response->assertSee('value="1006"', false);
});

test('storing sale saves the provided bill number', function () {
    $response = $this->actingAs($this->user)->post(route('customer-sales.store'), [
        'bill_number' => 2026,
        'customer_id' => $this->customer->id,
        'sale_type' => 'order',
        'date' => now()->toDateString(),
        'delivery_date' => now()->addDays(3)->toDateString(),
        'id' => [$this->product->id],
        'qty' => [1],
        'price' => [150],
        'amount' => [150],
        'delivered_qty' => [0],
        'vat' => 0,
        'vat_amount' => 0,
        'total_bill' => 150,
        'account_id' => [$this->account->id],
        'payment_amount' => [0],
        'payment_notes' => [''],
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('sales', [
        'bill_number' => 2026,
    ]);
});

test('edit sale view displays existing bill number and update modifies it', function () {
    $sale = sale::create([
        'bill_number' => 500,
        'customer_id' => $this->customer->id,
        'date' => now()->toDateString(),
        'sale_type' => 'order',
        'total' => 150,
        'total_bill' => 150,
        'refID' => 234567,
    ]);

    $response = $this->actingAs($this->user)->get(route('customer-sales.edit', $sale->id));

    $response->assertStatus(200);
    $response->assertSee('name="bill_number"', false);
    $response->assertSee('value="500"', false);

    $updateResponse = $this->actingAs($this->user)->put(route('customer-sales.update', $sale->id), [
        'bill_number' => 505,
        'customer_id' => $this->customer->id,
        'sale_type' => 'order',
        'date' => now()->toDateString(),
        'delivery_date' => now()->addDays(3)->toDateString(),
        'id' => [$this->product->id],
        'qty' => [1],
        'price' => [150],
        'amount' => [150],
        'delivered_qty' => [0],
        'vat' => 0,
        'vat_amount' => 0,
        'total_bill' => 150,
        'status' => 'pending',
    ]);

    $updateResponse->assertSessionHasNoErrors();
    $this->assertDatabaseHas('sales', [
        'id' => $sale->id,
        'bill_number' => 505,
    ]);
});

test('sales view displays bill number in receipt details', function () {
    $sale = sale::create([
        'bill_number' => 789,
        'customer_id' => $this->customer->id,
        'date' => now()->toDateString(),
        'sale_type' => 'order',
        'total' => 150,
        'total_bill' => 150,
        'refID' => 345678,
    ]);

    $response = $this->actingAs($this->user)->get(route('customer-sales.show', $sale->id));

    $response->assertStatus(200);
    $response->assertSee('Bill No.');
    $response->assertSee('#789');
});

test('sales index view displays bill number in table', function () {
    $sale = sale::create([
        'bill_number' => 888,
        'customer_id' => $this->customer->id,
        'date' => now()->toDateString(),
        'sale_type' => 'order',
        'total' => 150,
        'total_bill' => 150,
        'refID' => 456789,
    ]);

    $response = $this->actingAs($this->user)->get(route('customer-sales.index'));

    $response->assertStatus(200);
    $response->assertSee('Bill #');
    $response->assertSee('888');
});

test('global search finds sale by bill number', function () {
    $targetSale = sale::create([
        'bill_number' => 999123,
        'customer_id' => $this->customer->id,
        'date' => now()->toDateString(),
        'sale_type' => 'order',
        'total' => 150,
        'total_bill' => 150,
        'refID' => 567890,
    ]);

    $otherSale = sale::create([
        'bill_number' => 111456,
        'customer_id' => $this->customer->id,
        'date' => now()->toDateString(),
        'sale_type' => 'order',
        'total' => 150,
        'total_bill' => 150,
        'refID' => 678901,
    ]);

    $response = $this->actingAs($this->user)->get(route('customer-sales.index', ['search' => '999123']));

    $response->assertStatus(200);
    $response->assertSee('999123');
    $response->assertDontSee('111456');

    $hashResponse = $this->actingAs($this->user)->get(route('customer-sales.index', ['search' => '#999123']));

    $hashResponse->assertStatus(200);
    $hashResponse->assertSee('999123');
    $hashResponse->assertDontSee('111456');
});
