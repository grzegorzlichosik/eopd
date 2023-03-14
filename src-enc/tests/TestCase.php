<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    protected array $connectionsToTransact = ['mysql'];

    protected bool $seed = true;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Mail::fake();
    }
}
