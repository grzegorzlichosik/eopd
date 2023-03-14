<?php

namespace App\Services\Traits;

use Carbon\Carbon;

trait StartAndEndDates
{
    /**
     *
     * @codeCoverageIgnore
     */
    public static function getStartAndEndDates(?array $data): array
    {
        return [
            !empty($data['start']) ? Carbon::parse($data['start']) : now()->startOfWeek(),
            !empty($data['end']) ? Carbon::parse($data['end']) : now()->endOfWeek()
        ];
    }
}
