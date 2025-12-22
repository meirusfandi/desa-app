<?php

declare(strict_types=1);

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

if (! function_exists('app_setting')) {
    /**
     * Safely fetch a setting from the `settings` table.
     *
     * Returns $default if the table/connection is not available.
     */
    function app_setting(string $key, mixed $default = null): mixed
    {
        try {
            if (! Schema::hasTable('settings')) {
                return $default;
            }

            $value = Setting::query()->where('key', $key)->value('value');

            return ($value === null || $value === '') ? $default : $value;
        } catch (Throwable) {
            return $default;
        }
    }
}
