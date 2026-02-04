<?php

namespace Tests\Feature;

use Tests\TestCase;

class CourseProgressLabelTest extends TestCase
{
    public function test_blade_contains_learning_again_condition()
    {
        $blade = file_get_contents(resource_path('views/course-detail.blade.php'));

        // Ensure the progress == 100 case is present in the template (whitespace-tolerant)
        $this->assertMatchesRegularExpression('/@elseif\\s*\(\\$progress\\s*>=\\s*100\\)/', $blade);
        $this->assertStringContainsString('Learning again', $blade);

        // Ensure the previous 'Continue Learning' fallback still exists for <100%
        $this->assertStringContainsString('Continue Learning', $blade);
    }
}
