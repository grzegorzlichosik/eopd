<?php

if (!function_exists('getErrorMessage')) {
    function getErrorMessage(\Exception $e): string
    {
        if (config('app.debug', false) === true) {
            return $e->getMessage();
        }

        return trans('errors.whoops_something_went_wrong');
    }
}

if (!function_exists('cleanupAppends')) {
    function cleanupAppends(array $data): array
    {
        if (!empty($data['q'])) {
            unset($data['q']);
        }

        return $data;
    }
}

if (!function_exists('filterArrayByAllowedIndexes')) {
    function filterArrayByAllowedIndexes(array $array, array $indexes): array
    {
        return array_filter(
            $array,
            function ($key) use ($indexes) {
                return in_array($key, $indexes);
            },
            ARRAY_FILTER_USE_KEY
        );

    }
}

if (!function_exists('getTableSorting')) {
    function getTableSorting(string $defaultColumnName = 'name'): array
    {
        $request = request()->all();
        return [
            !empty($request['sortField']) ? $request['sortField'] : $defaultColumnName,
            !empty($request['sortOrder']) && $request['sortOrder'] === '-1' ? 'DESC' : 'ASC',
        ];
    }
}

if (!function_exists('generatePassword')) {
    function generatePassword(int $length = 32): string
    {
        return substr(str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890!$%^&!$%^&'), 0, $length);
    }
}

if (!function_exists('convertToDropdownData')) {
    function convertToDropdownData(Illuminate\Support\Collection $data, array $cols = ['name', 'uuid']): array
    {
        return $data->map(function ($item) use ($cols) {
            return [
                'name'  => $item[$cols[0]],
                'value' => $item[$cols[1]],
            ];
        })
            ->toArray();
    }
}
