<?php

declare(strict_types=1);

namespace Cutpick;

/**
 * The ranking of candidates: heaviest first, input order for equal weights.
 *
 * @phpstan-import-type Item from Registry
 */
final class Rank
{
    /**
     * @param list<Item> $items
     * @return list<Item>
     */
    public static function ranking(array $items): array
    {
        $ranked = $items;
        usort($ranked, static fn (array $a, array $b): int => $b['weight'] <=> $a['weight']);

        return $ranked;
    }
}
