<?php

declare(strict_types=1);

namespace Cutpick;

/**
 * The hash marks of a value against a full bar.
 */
final class Bars
{
    /**
     * value * 20 / full rounded down, at least one mark for a value above 0 and at most 20; no mark for 0.
     */
    public static function bar(int $value, int $full): string
    {
        if ($value <= 0 || $full <= 0) {
            return '';
        }

        return str_repeat('#', min(20, max(1, intdiv($value * 20, $full))));
    }
}
