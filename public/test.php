<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/sale/deliver/3', 'POST', [
    'deliver_now' => [2 => 5],
    'date' => '2026-10-04'
]);

// Need to bypass CSRF, so let's just resolve the controller
$controller = $app->make(\App\Http\Controllers\SaleController::class);
try {
    $response = $controller->storeDelivery($request, 3);
    echo "Success! Response: " . get_class($response);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
}
