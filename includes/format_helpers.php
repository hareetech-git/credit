<?php

if (!function_exists('format_inr')) {
    function format_inr($amount, int $decimals = 0): string
    {
        $num = (float)$amount;
        $neg = $num < 0 ? '-' : '';
        $num = abs($num);

        $fixed = number_format($num, $decimals, '.', '');
        $parts = explode('.', $fixed);
        $intPart = $parts[0];
        $decimalPart = $parts[1] ?? '';

        if (strlen($intPart) > 3) {
            $last3 = substr($intPart, -3);
            $rest = substr($intPart, 0, -3);
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $intPart = $rest . ',' . $last3;
        }

        if ($decimals > 0) {
            return $neg . $intPart . '.' . $decimalPart;
        }

        return $neg . $intPart;
    }
}
