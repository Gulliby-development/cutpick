<?php

declare(strict_types=1);

namespace Cutpick\Commands;

use Cutpick\Failure;
use Cutpick\Filters;
use Cutpick\Rank;
use Cutpick\Report;

/**
 * The topk command: the k heaviest candidates.
 *
 * @phpstan-import-type Item from \Cutpick\Registry
 * @phpstan-import-type Options from \Cutpick\Registry
 * @phpstan-import-type Reply from \Cutpick\Registry
 */
final class TopkCommand
{
    /**
     * @param Options $options
     * @param list<Item> $items
     * @return Reply
     */
    public static function run(array $options, array $items): array
    {
        $k = $options['k'];
        if ($k === null) {
            throw new Failure(2, 'missing --k');
        }

        $kept = Filters::apply(Rank::ranking($items), 'topk', $k);

        return [
            'status' => 0,
            'out' => Report::renderRows(Filters::table($kept), $options['format']),
            'err' => '',
        ];
    }
}
