<?php

namespace Tests\Unit\Helpers;

include_once __DIR__ . '/../../../app/Helpers/helpers.php';

use Tests\TestCase;

class GetErrorMessageTest extends TestCase
{

    public function test_get_error_message_helper()
    {
        $message = 'Test Message';
        $e = new \Exception($message);

        $this->assertEquals(getErrorMessage($e), $message);

        config(['app.debug' => false]);
        $message = trans('errors.whoops_something_went_wrong');
        $this->assertEquals(getErrorMessage($e), $message);
    }

}
