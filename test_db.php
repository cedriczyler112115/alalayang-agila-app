<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $u = App\Models\User::first();
    $p = $u->subscriptionPayments()->create(['receipt_path' => 'test.png', 'status' => 'pending']);
    echo 'SUCCESS: ' . $p->id;
} catch(\Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
