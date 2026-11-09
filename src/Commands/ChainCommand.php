<?php

declare(strict_types=1);

namespace Cutpick\Commands;

use Cutpick\Failure;
use Cutpick\Filters;
use Cutpick\Rank;
use Cutpick\Report;

/**
 * The chain command: the steps one after the other, each on what the step before kept.
 *
 * @phpstan-import-type Item from \Cutpick\Registry
 * @phpstan-import-type Options from \Cutpick\Registry
 * @phpstan-import-type Reply from \Cutpick\Registry
 */
final class ChainCommand
{
    /**
     * @param Options $options
     * @param list<Item> $items
     * @return Reply
     */
    public static function run(array $options, array $items): array
    {
        $steps = $options['steps'];
        if ($steps === null) {
            throw new Failure(2, 'missing --steps');
        }

        $kept = Rank::ranking($items);
        foreach (explode(',', $steps) as $step) {
            [$kind, $value] = explode(':', $step, 2) + [1 => '0'];
            $kept = Filters::apply($kept, $kind, (int) $value);
        }

        return [
            'status' => 0,
            'out' => Report::renderRows(Filters::table($kept), $options['format']),
            'err' => '',
        ];
    }
}
