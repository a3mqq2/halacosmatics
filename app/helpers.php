<?php

use Carbon\Carbon;

if (! function_exists('dt')) {
    function dt(Carbon|string|null $date, bool $withTime = true): string
    {
        if (! $date) return '—';

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        if (! $withTime) {
            return $carbon->format('d/m/Y');
        }

        $period = $carbon->format('A') === 'AM' ? 'ص' : 'م';

        return $carbon->format('d/m/Y h:i') . ' ' . $period;
    }
}
