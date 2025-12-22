<?php

namespace Tests\Unit;

use App\View\Components\GuestLayout;
use Tests\TestCase;

class GuestLayoutTest extends TestCase
{
    public function test_it_renders_guest_layout_view(): void
    {
        $component = new GuestLayout();
        $view = $component->render();

        $this->assertSame('layouts.guest', $view->name());
    }
}
