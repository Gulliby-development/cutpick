<?php

declare(strict_types=1);

namespace Cutpick\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EdgesRowsTest extends TestCase
{
    /**
     * @param list<string> $args
     */
    #[DataProvider('rows')]
    public function testRow(array $args, string $input, int $status, string $out, string $err): void
    {
        Harness::assertReply($args, $input, $status, $out, $err);
    }

    /**
     * @return list<array{list<string>, string, int, string, string}>
     */
    public static function rows(): array
    {
        return [
            [["check"], "a 999999\nb 999999\n", 0, "ok, 2 candidates, total 1999998\n", ""],
            [["share"], "a 999999\nb 0\n", 0, "a 1000\nb 0\n", ""],
            [["topk", "--k", "1"], "a 999999\nb 999999\n", 0, "a 999999\n", ""],
            [["share", "--unit", "ppm"], "a 1\nb 999999\n", 0, "a 1\nb 999999\n", ""],
            [["share"], "a 1\nb 0\nc 0\n", 0, "a 1000\nb 0\nc 0\n", ""],
            [["minp", "--ratio", "1000"], "a 5\nb 5\nc 4\n", 0, "a 5\nb 5\n", ""],
            [["check"], "a 0\n# b 1\nc 1\n", 0, "ok, 2 candidates, total 1\n", ""],
            [["draw", "--at", "999998"], "a 999999\n", 0, "a\n", ""],
            [["chain", "--steps", "topp:1"], "a 0\nb 1\n", 0, "b 1\n", ""],
        ];
    }
}
