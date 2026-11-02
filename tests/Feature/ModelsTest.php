<?php

use App\Models\models;
use App\Models\products;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->product = products::create([
        'name' => 'Classic Black Abaya',
        'type' => 'Ready Made',
        'price' => 250.00,
        'unit' => 'Pcs',
        'is_active' => true,
    ]);
});

test('models index page can be rendered for authenticated users', function () {
    models::create([
        'product_id' => $this->product->id,
        'model_number' => 'MDL-001',
    ]);

    $response = $this->actingAs($this->user)->get(route('models.index'));

    $response->assertStatus(200);
    $response->assertSee('Abaya Models');
    $response->assertSee('Classic Black Abaya');
    $response->assertSee('MDL-001');
});

test('storing a new model succeeds with valid data', function () {
    $response = $this->actingAs($this->user)->post(route('models.store'), [
        'product_id' => $this->product->id,
        'model_number' => 'MDL-2026-A',
    ]);

    $response->assertSessionHas('success', 'Model Created Successfully');

    $this->assertDatabaseHas('models', [
        'product_id' => $this->product->id,
        'model_number' => 'MDL-2026-A',
    ]);
});

test('storing a new model also works if field is named product', function () {
    $response = $this->actingAs($this->user)->post(route('models.store'), [
        'product' => $this->product->id,
        'model_number' => 'MDL-LEGACY-01',
    ]);

    $response->assertSessionHas('success', 'Model Created Successfully');

    $this->assertDatabaseHas('models', [
        'product_id' => $this->product->id,
        'model_number' => 'MDL-LEGACY-01',
    ]);
});

test('storing a model fails if required fields are missing', function () {
    $response = $this->actingAs($this->user)->post(route('models.store'), []);

    $response->assertSessionHasErrors(['product_id', 'model_number']);
});

test('updating an existing model succeeds with valid data', function () {
    $model = models::create([
        'product_id' => $this->product->id,
        'model_number' => 'MDL-OLD',
    ]);

    $newProduct = products::create([
        'name' => 'Embroidered Silk Abaya',
        'type' => 'Ready Made',
        'price' => 350.00,
        'unit' => 'Pcs',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->put(route('models.update', $model->id), [
        'product_id' => $newProduct->id,
        'model_number' => 'MDL-NEW',
    ]);

    $response->assertSessionHas('success', 'Model Updated Successfully');

    $this->assertDatabaseHas('models', [
        'id' => $model->id,
        'product_id' => $newProduct->id,
        'model_number' => 'MDL-NEW',
    ]);
});

test('updating a model fails if required fields are missing', function () {
    $model = models::create([
        'product_id' => $this->product->id,
        'model_number' => 'MDL-TEMP',
    ]);

    $response = $this->actingAs($this->user)->put(route('models.update', $model->id), [
        'product_id' => '',
        'model_number' => '',
    ]);

    $response->assertSessionHasErrors(['product_id', 'model_number']);
});
