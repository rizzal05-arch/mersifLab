<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Detailed Video Upload Debug ===\n\n";

// Test 1: Check current controller code
echo "1. Current Controller Code Analysis:\n";
$controllerPath = app_path('Http/Controllers/Teacher/ModuleController.php');
$controllerContent = file_get_contents($controllerPath);

// Find storeVideo method
if (preg_match('/public function storeVideo.*?\{(.*?)\}/s', $controllerContent, $matches)) {
    $storeVideoCode = $matches[1];
    echo "   ✅ Found storeVideo method\n";
    
    // Check for file handling
    if (strpos($storeVideoCode, '$file = $request->file(\'file\')') !== false) {
        echo "   ✅ File extraction found\n";
        
        // Check for null check
        if (strpos($storeVideoCode, 'if (!$file)') !== false) {
            echo "   ✅ Null check found\n";
        } else {
            echo "   ❌ No null check - THIS IS THE PROBLEM!\n";
        }
        
        // Check for storeAs call
        if (strpos($storeVideoCode, 'storeAs') !== false) {
            echo "   ✅ storeAs found\n";
            
            // Extract the path
            if (preg_match('/storeAs\([\'"]([^\'"]+)[\'"],?\s*\$fileName,\s*[\'"]([^\'"]+)[\'"]/', $storeVideoCode, $pathMatches)) {
                $disk = $pathMatches[2] ?? 'unknown';
                $folder = $pathMatches[1] ?? 'unknown';
                echo "   ✅ Storage path: {$disk}://{$folder}\n";
                
                if ($disk === 'private' && $folder === 'files/videos') {
                    echo "   ✅ Using correct private storage\n";
                } else {
                    echo "   ❌ Incorrect storage configuration\n";
                }
            } else {
                echo "   ❌ storeAs pattern not found\n";
            }
        } else {
            echo "   ❌ storeAs not found\n";
        }
        
        // Check for empty path issue
        if (strpos($storeVideoCode, '$path') !== false) {
            echo "   ✅ Path variable found\n";
            
            // Check if path could be empty
            if (strpos($storeVideoCode, 'getClientOriginalExtension()') !== false) {
                echo "   ✅ File extension handling found\n";
            }
            
            if (strpos($storeVideoCode, 'time()') !== false) {
                echo "   ✅ Timestamp generation found\n";
            }
        } else {
            echo "   ❌ Path variable not found\n";
        }
    } else {
        echo "   ❌ File extraction not found\n";
    }
} else {
    echo "   ❌ storeVideo method not found\n";
}

// Test 2: Check storage configuration
echo "\n2. Storage Configuration Test:\n";
try {
    $storage = \Storage::disk('private');
    echo "   ✅ Private storage accessible\n";
    
    // Test actual file storage
    $testContent = 'test video content';
    $testPath = 'files/videos/test-upload.txt';
    
    $result = $storage->put($testPath, $testContent);
    echo "   ✅ Storage put result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($storage->exists($testPath)) {
        echo "   ✅ File exists after upload\n";
        $content = $storage->get($testPath);
        echo "   ✅ File content readable: " . ($content === $testContent ? 'YES' : 'NO') . "\n";
        
        // Test getRealPath or similar
        try {
            $adapter = $storage->getAdapter();
            echo "   ✅ Storage adapter: " . get_class($adapter) . "\n";
        } catch (Exception $e) {
            echo "   ❌ Adapter error: " . $e->getMessage() . "\n";
        }
        
        $storage->delete($testPath);
        echo "   ✅ File cleanup: SUCCESS\n";
    } else {
        echo "   ❌ File not found after upload\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Storage error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

// Test 3: Check Laravel version and filesystem
echo "\n3. Environment Check:\n";
echo "   Laravel Version: " . app()->version() . "\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   Storage Default: " . config('filesystems.default') . "\n";

$disks = config('filesystems.disks');
if (isset($disks['private'])) {
    echo "   ✅ Private disk configured\n";
    echo "   Private driver: " . $disks['private']['driver'] . "\n";
    echo "   Private root: " . $disks['private']['root'] . "\n";
} else {
    echo "   ❌ Private disk not configured\n";
}

echo "\n=== Debug Complete ===\n";
echo "If you see '❌ No null check', that's the cause of 'Path must not be empty' error!\n";
