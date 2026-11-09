<?php

declare(strict_types=1);

namespace Cutpick;

/**
 * One filter step (topk, topp or minp) on a ranked list, and the table of what it keeps.
 *
 * @phpstan-import-type Item from Registry
 * @phpstan-import-type Row from Registry
 */
final class Filters
{
    /**
     * @param list<Item> $ranked
     * @return list<Item>
     */
    public static function apply(array $ranked, string $kind, int $value): array
    {
        if ($kind === 'topk') {
            return array_slice($ranked, 0, $value);
        }

        if ($kind === 'topp') {
            $total = self::total($ranked);
            $kept = 0;
            $result = [];
            foreach ($ranked as $item) {
                $result[] = $item;
                $kept += $item['weight'];
                if ($kept * 1000 >= $value * $total) {
                    break;
                }
            }

            return $result;
        }

        if ($kind === 'minp') {
            $heaviest = self::heaviest($ranked);
            $result = [];
            foreach ($ranked as $item) {
                if ($item['weight'] * 1000 >= $value * $heaviest) {
                    $result[] = $item;
                }
            }

            return $result;
        }

        throw new Failure(1, 'unknown filter ' . $kind);
    }

    /**
     * @param list<Item> $items
     * @return list<Row>
     */
    public static function table(array $items): array
    {
        $full = self::heaviest($items);
        $rows = [];
        foreach ($items as $item) {
            $rows[] = ['token' => $item['token'], 'number' => $item['weight'], 'full' => $full];
        }

        return $rows;
    }

    /**
     * @param list<Item> $items
     */
    private static function total(array $items): int
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['weight'];
        }

        return $total;
    }

    /**
     * @param list<Item> $items
     */
    private static function heaviest(array $items): int
    {
        $heaviest = 0;
        foreach ($items as $item) {
            $heaviest = max($heaviest, $item['weight']);
        }

        return $heaviest;
    }
}
