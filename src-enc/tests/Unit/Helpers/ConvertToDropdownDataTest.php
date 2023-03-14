<?php

namespace Tests\Unit\Helpers;

include_once __DIR__ . '/../../../app/Helpers/helpers.php';

use Illuminate\Support\Str;
use Tests\TestCase;

class ConvertToDropdownDataTest extends TestCase
{

    public function test_convert_to_dropdown_data_helper()
    {
        $array = [];
        for ($i = 0; $i < 10; $i++) {
            $array[] = [
                'name' => Str::random(),
                'uuid' => Str::uuid()->toString(),
            ];
        };

        $result = convertToDropdownData(collect($array));

        foreach ($result as $key => $row) {
            $this->assertEquals($array[$key]['name'], $row['name']);
            $this->assertEquals($array[$key]['uuid'], $row['value']);
        }
    }
}
