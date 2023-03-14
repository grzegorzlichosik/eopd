<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->browse(function (Browser $browser){
            $browser->visit('/')
                ->assertSee('Secure Login');
        });
    }
}
