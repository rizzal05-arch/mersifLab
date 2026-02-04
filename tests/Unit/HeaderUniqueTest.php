<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HeaderUniqueTest extends TestCase
{
    protected function projectRoot(): string
    {
        return realpath(__DIR__ . '/../../');
    }

    public function test_layout_includes_header_only_once()
    {
        $layoutPath = $this->projectRoot() . '/resources/views/layouts/app.blade.php';
        $layout = file_get_contents($layoutPath);
        $this->assertIsString($layout);

        $includeCount = substr_count($layout, "@include('layouts.header')");
        $this->assertSame(1, $includeCount, "layouts/app.blade.php should include the header exactly once");
    }

    public function test_footer_does_not_contain_navbar_markups()
    {
        $footerPath = $this->projectRoot() . '/resources/views/layouts/footer.blade.php';
        $footer = file_get_contents($footerPath);
        $this->assertIsString($footer);

        $this->assertStringNotContainsString('<nav class="navbar', $footer, 'Footer must not contain a <nav class="navbar" element');
        $this->assertStringNotContainsString('navbar-brand', $footer, 'Footer must not include header branding markup');
    }
}
