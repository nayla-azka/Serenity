<?php

use Carbon\Carbon;

if (! function_exists('formatDateTz')) {
    function formatDateTz($date, $format = 'd M Y H:i')
    {
        if (! $date) {
            return '-';
        }

        $tz = session('timezone', config('app.timezone', 'UTC'));

        try {
            return Carbon::parse($date)->timezone($tz)->format($format);
        } catch (\Exception $e) {
            return $date; // fallback kalau gagal
        }
    }
}
