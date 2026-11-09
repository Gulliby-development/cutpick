<?php

declare(strict_types=1);

namespace Cutpick\Commands;

use Cutpick\Failure;
use Cutpick\Filters;
use Cutpick\Rank;
use Cutpick\Report;

/**
 * The minp command: keeps the candidates that weigh at least a ratio of the heaviest one.
 *
 * @phpstan-import-type Item from \Cutpick\Registry
 * @phpstan-import-type Options from \Cutpick\Registry
 * @phpstan-import-type Reply from \Cutpick\Registry
 */
final class MinpCommand
{
    /**
     * @param Options $options
     * @param list<Item> $items
     * @return Reply
     */
    public static function run(array $options, array $items): array
    {
        $ratio = $options['ratio'];
        if ($ratio === null) {
            throw new Failure(2, 'missing --ratio');
        }

        $kept = Filters::apply(Rank::ranking($items), 'minp', $ratio);

        return [
            'status' => 0,
            'out' => Report::renderRows(Filters::table($kept), $options['format']),
            'err' => '',
        ];
    }
}
