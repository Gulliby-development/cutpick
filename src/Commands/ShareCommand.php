<?php

declare(strict_types=1);

namespace Cutpick\Commands;

use Cutpick\Registry;
use Cutpick\Report;

/**
 * The share command: each weight as a share of the total in the chosen unit.
 *
 * @phpstan-import-type Item from \Cutpick\Registry
 * @phpstan-import-type Options from \Cutpick\Registry
 * @phpstan-import-type Reply from \Cutpick\Registry
 */
final class ShareCommand
{
    /**
     * @param Options $options
     * @param list<Item> $items
     * @return Reply
     */
    public static function run(array $options, array $items): array
    {
        $scale = Registry::UNITS[$options['unit']] ?? Registry::UNITS['permille'];
        $total = 0;
        foreach ($items as $item) {
            $total += $item['weight'];
        }

        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                'token' => $item['token'],
                'number' => intdiv($item['weight'] * $scale + intdiv($total, 2), $total),
                'full' => $scale,
            ];
        }

        return [
            'status' => 0,
            'out' => Report::renderRows($rows, $options['format']),
            'err' => '',
        ];
    }
}
