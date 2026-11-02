<?php

use App\Models\accounts;
use App\Models\ManufacturingOrder;
use App\Models\models;
use App\Models\products;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->tailor = accounts::create([
        'title' => 'Master Tailor',
        'type' => 'Tailor',
        'is_active' => true,
    ]);
    $this->account = accounts::create([
        'title' => 'Bank Account',
        'type' => 'Business',
        'is_active' => true,
    ]);
    $this->product = products::create([
        'name' => 'Embroidered Kaftan',
        'type' => 'Ready Made',
        'price' => 300.00,
        'unit' => 'Pcs',
        'is_active' => true,
    ]);
    $this->model = models::create([
        'product_id' => $this->product->id,
        'model_number' => 'KAFTAN-2026-01',
    ]);
});

test('manufacturing order create view displays model select and product models', function () {
    $response = $this->actingAs($this->user)->get(route('tailor_orders.create'));

    $response->assertStatus(200);
    $response->assertSee('<th class="p-1">Model</th>', false);
    $response->assertSee('<select name="model[]"', false);
    $response->assertSee('KAFTAN-2026-01');
});

test('storing manufacturing order saves selected model', function () {
    $response = $this->actingAs($this->user)->post(route('tailor_orders.store'), [
        'date' => now()->toDateString(),
        'tailor_id' => $this->tailor->id,
        'payment_type' => 'Unpaid',
        'total_amount' => 500,
        'product_id' => [$this->product->id],
        'type' => ['Order'],
        'model' => ['KAFTAN-2026-01'],
        'order_qty' => [5],
        'received_qty' => [0],
    ]);

    $response->assertRedirect(route('tailor_orders.index'));

    $this->assertDatabaseHas('manufacturing_order_products', [
        'product_id' => $this->product->id,
        'type' => 'Order',
        'model' => 'KAFTAN-2026-01',
        'order_qty' => 5,
    ]);
});

test('manufacturing order edit view displays saved model in select', function () {
    $order = ManufacturingOrder::create([
        'date' => now()->toDateString(),
        'tailor_id' => $this->tailor->id,
        'payment_type' => 'Unpaid',
        'total_amount' => 500,
        'refID' => 112233,
    ]);

    $order->products()->create([
        'product_id' => $this->product->id,
        'type' => 'Order',
        'model' => 'KAFTAN-2026-01',
        'order_qty' => 5,
        'received_qty' => 0,
    ]);

    $response = $this->actingAs($this->user)->get(route('tailor_orders.edit', $order->id));

    $response->assertStatus(200);
    $response->assertSee('<select name="model[]"', false);
    $response->assertSee('KAFTAN-2026-01');
});
