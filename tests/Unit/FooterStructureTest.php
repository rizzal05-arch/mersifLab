<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FooterStructureTest extends TestCase
{
    protected function projectRoot(): string
    {
        return realpath(__DIR__ . '/../../');
    }

    public function test_app_layout_includes_footer()
    {
        $path = $this->projectRoot() . '/resources/views/layouts/app.blade.php';
        $content = file_get_contents($path);
        $this->assertStringContainsString("@include('layouts.footer')", $content);
    }

    public function test_footer_has_container_and_footer_section()
    {
        $path = $this->projectRoot() . '/resources/views/layouts/footer.blade.php';
        $content = file_get_contents($path);

        $this->assertStringContainsString('<footer class="footer-section"', $content);
        $this->assertStringContainsString('class="container', $content, 'Footer must contain a .container to constrain inner content');
        $this->assertStringContainsString('class="footer-bottom"', $content, 'Footer must include a .footer-bottom copyright band');
    }
}
