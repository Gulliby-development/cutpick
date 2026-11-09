<?php

declare(strict_types=1);

namespace Cutpick;

/**
 * The table renderer and the plural helper.
 *
 * @phpstan-import-type Row from Registry
 */
final class Report
{
    /**
     * @param list<Row> $rows
     */
    public static function renderRows(array $rows, string $format): string
    {
        $out = '';
        foreach ($rows as $row) {
            $token = $row['token'];
            $number = $row['number'];
            if ($format === 'csv') {
                $out .= $token . ',' . $number . "\n";
            } elseif ($format === 'bar') {
                $out .= $token . ' ' . $number . ' ' . Bars::bar($number, $row['full']) . "\n";
            } else {
                $out .= $token . ' ' . $number . "\n";
            }
        }

        return $out;
    }

    public static function plural(int $count, string $noun): string
    {
        return $count . ' ' . $noun . ($count === 1 ? '' : 's');
    }
}
