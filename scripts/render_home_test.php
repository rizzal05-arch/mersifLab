<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$testimonials = App\Models\Testimonial::where('is_published', true)->orderBy('created_at', 'desc')->take(3)->get();
$view = view('home', [
    'categories' => collect(),
    'coursesByCategory' => [],
    'trendingCourses' => collect(),
    'enrolledCourses' => collect(),
    'teacherCourses' => collect(),
    'testimonials' => $testimonials,
]);

$html = $view->render();

if (strpos($html, 'Auto Test') !== false) {
    echo "FOUND\n";
} else {
    // output first 800 chars to inspect
    echo "NOTFOUND\n";
    echo substr($html, 0, 800);
}
