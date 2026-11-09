<?php

declare(strict_types=1);

namespace Cutpick\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CliRowsTest extends TestCase
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
            [["check"], "the 500\ncat 300\ndog 150\nsat 40\nmat 10\n", 0, "ok, 5 candidates, total 1000\n", ""],
            [["share"], "a 1\nb 3\n", 0, "a 250\nb 750\n", ""],
            [["share", "--unit", "percent", "--format", "csv"], "a 1\nb 1\n", 0, "a,50\nb,50\n", ""],
            [["topk", "--k", "2"], "the 500\ncat 300\ndog 150\nsat 40\nmat 10\n", 0, "the 500\ncat 300\n", ""],
            [["minp", "--ratio", "100"], "the 500\ncat 300\ndog 150\nsat 40\nmat 10\n", 0, "the 500\ncat 300\ndog 150\n", ""],
            [["share", "--format", "bar"], "a 1\nb 3\n", 0, "a 250 #####\nb 750 ###############\n", ""],
            [["chain", "--steps", "topk:3,minp:200"], "the 500\ncat 300\ndog 150\nsat 40\nmat 10\n", 0, "the 500\ncat 300\ndog 150\n", ""],
            [["draw", "--at", "520"], "the 500\ncat 300\ndog 150\nsat 40\nmat 10\n", 0, "cat\n", ""],
        ];
    }
}
