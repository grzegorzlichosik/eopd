<?php

namespace Tests\Unit\Helpers;

include_once __DIR__ . '/../../../app/Helpers/helpers.php';

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

class FilterArrayByAllowedIndexesTest extends TestCase
{

    private int $counter = 6;

    public function test_filter_array_by_allowed_indexes_helper()
    {
        $testArray = [];
        $keys = [];

        for($i = 0; $i < 10; $i++){
            $key = 'test_key_' . $i;
            $testArray[$key] = Str::random();
            $keys[] = $key;
        }

        $allowedKeys = Arr::random($keys, $this->counter);
        $filteredArray = filterArrayByAllowedIndexes($testArray, $allowedKeys);
        $notAllowedKeys = array_diff_key($testArray, $filteredArray);

        foreach ($allowedKeys as $key){
            $this->assertArrayHasKey($key, $filteredArray);
        }

        foreach ($allowedKeys as $key){
            $this->assertArrayNotHasKey($key, $notAllowedKeys);
        }
    }

}
