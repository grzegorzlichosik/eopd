<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class Register extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/register';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@name'=>'input[id=name]',
            '@email' => 'input[id=email]',
            '@phone_number'=>'input[id=phone_number]',
            '@organisation_name' => 'input[id=organisation_name]',
            '@organisation_address' => 'input[id=organisation_address]',
            '@register' => 'button[type=submit]',
        ];
    }
}
