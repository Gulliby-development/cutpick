<?php

declare(strict_types=1);

namespace Cutpick\Commands;

use Cutpick\Report;

/**
 * The check command: reports the number of candidates and the total weight.
 *
 * @phpstan-import-type Item from \Cutpick\Registry
 * @phpstan-import-type Options from \Cutpick\Registry
 * @phpstan-import-type Reply from \Cutpick\Registry
 */
final class CheckCommand
{
    /**
     * @param Options $options
     * @param list<Item> $items
     * @return Reply
     */
    public static function run(array $options, array $items): array
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['weight'];
        }

        return [
            'status' => 0,
            'out' => 'ok, ' . Report::plural(count($items), 'candidate') . ', total ' . $total . "\n",
            'err' => '',
        ];
    }
}
