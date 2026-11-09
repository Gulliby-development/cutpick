<?php

declare(strict_types=1);

namespace Cutpick\Commands;

use Cutpick\Failure;

/**
 * The draw command: names the candidate that a position in the cumulative weights falls on.
 *
 * @phpstan-import-type Item from \Cutpick\Registry
 * @phpstan-import-type Options from \Cutpick\Registry
 * @phpstan-import-type Reply from \Cutpick\Registry
 */
final class DrawCommand
{
    /**
     * @param Options $options
     * @param list<Item> $items
     * @return Reply
     */
    public static function run(array $options, array $items): array
    {
        $at = $options['at'] ?? 0;
        $total = 0;
        foreach ($items as $item) {
            $total += $item['weight'];
        }

        if ($at >= $total) {
            throw new Failure(1, 'draw position ' . $at . ' is outside the total ' . $total);
        }

        $running = 0;
        foreach ($items as $item) {
            $running += $item['weight'];
            if ($running > $at) {
                return ['status' => 0, 'out' => $item['token'] . "\n", 'err' => ''];
            }
        }

        throw new Failure(1, 'draw position ' . $at . ' is outside the total ' . $total);
    }
}
