<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TeacherApplication;

$model = new TeacherApplication();
$fillable = $model->getFillable();

echo "=== Portfolio Link Fix Verification ===\n\n";
echo "Fillable fields:\n";
foreach ($fillable as $field) {
    echo "  - " . $field . "\n";
}

echo "\n";
if (in_array('portfolio_link', $fillable)) {
    echo "✅ SUCCESS! portfolio_link is now in the fillable array.\n";
    echo "   Portfolio link updates should now work correctly.\n";
} else {
    echo "❌ ERROR! portfolio_link is still NOT in the fillable array.\n";
}
echo "\n";
