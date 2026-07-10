<?php

if (! function_exists('money')) {
    function money(?float $n): string
    {
        if ($n === null) {
            return '—';
        }

        return '$'.number_format(round($n), 0);
    }
}
