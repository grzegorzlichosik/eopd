<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Notification extends LaravelNotification implements ShouldQueue
{
    use Queueable;

    final public static function getAppName(): string
    {
        return env('APP_NAME', 'Laravel');
    }

    final public static function getAppUrl(): string
    {
        return env('APP_URL', 'https://localhost');
    }

    public static function getSalutation(): string
    {
        return "\r\n\r\nKind regards,  \r\n\n" . env('APP_NAME') . " team";
    }

}
