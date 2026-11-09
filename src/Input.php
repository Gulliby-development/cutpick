<?php

declare(strict_types=1);

namespace Cutpick;

/**
 * Reads the list of candidates from the text of the input.
 *
 * @phpstan-import-type Item from Registry
 */
final class Input
{
    /**
     * @return list<Item>
     */
    public static function read(string $text): array
    {
        $lines = [];
        if ($text !== '') {
            $lines = explode("\n", $text);
            if (str_ends_with($text, "\n")) {
                array_pop($lines);
            }
        }

        if (count($lines) > Registry::MAX_LINES) {
            throw new Failure(1, 'input is too long (max ' . Registry::MAX_LINES . ' lines)');
        }

        $items = [];
        $seen = [];
        $total = 0;
        foreach ($lines as $index => $line) {
            $number = $index + 1;
            if (str_ends_with($line, "\r")) {
                $line = substr($line, 0, -1);
            }
            $line = trim($line, " \t");
            if ($line === '') {
                continue;
            }
            $words = preg_split('/[ \t]+/', $line);
            if ($words === false) {
                continue;
            }
            if (str_starts_with($words[0], '#')) {
                continue;
            }
            if (count($words) !== 2) {
                throw new Failure(1, 'line ' . $number . ': expected TOKEN WEIGHT');
            }
            [$token, $weight] = $words;
            if (preg_match('/^[A-Za-z0-9_.-]{1,12}$/', $token) !== 1) {
                throw new Failure(1, 'line ' . $number . ': bad token ' . $token);
            }
            if (preg_match('/^(0|[1-9][0-9]{0,5})$/', $weight) !== 1) {
                throw new Failure(1, 'line ' . $number . ': bad weight ' . $weight);
            }
            if (in_array($token, $seen, true)) {
                throw new Failure(1, 'line ' . $number . ': duplicate token ' . $token);
            }
            $seen[] = $token;
            $items[] = ['token' => $token, 'weight' => (int) $weight];
            $total += (int) $weight;
        }

        if ($items === []) {
            throw new Failure(1, 'no candidates');
        }
        if ($total === 0) {
            throw new Failure(1, 'total weight is zero');
        }

        return $items;
    }
}
