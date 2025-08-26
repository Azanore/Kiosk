<?php
declare(strict_types=1);

class Format
{
    public static function money(float $amount): string
    {
        return number_format($amount, 2, ',', ' ') . ' DH';
    }
}
