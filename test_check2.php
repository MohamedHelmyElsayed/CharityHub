<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = \App\Models\Certificate::latest('id')->first();
if ($c) {
    $path1 = storage_path('app/' . $c->certificate_path);
    $path2 = storage_path('app/private/' . $c->certificate_path);
    
    echo "Path1: " . $path1 . " -> Exists: " . (file_exists($path1) ? "Yes" : "No") . "\n";
    echo "Path2: " . $path2 . " -> Exists: " . (file_exists($path2) ? "Yes" : "No") . "\n";
} else {
    echo "No certificates.\n";
}
