<?php

declare(strict_types=1);

namespace Cutpick\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ReadsRowsTest extends TestCase
{
    /**
     * @param list<string> $args
     */
    #[DataProvider('rows')]
    public function testRow(array $args, string $input, int $status, string $out, string $err): void
    {
        Harness::assertReads($args, $status);
    }

    /**
     * @return list<array{list<string>, string, int, string, string}>
     */
    public static function rows(): array
    {
        return [
            [["check"], "", 1, "", ""],
            [["share"], "", 1, "", ""],
            [["share", "--unit", "ppm"], "", 1, "", ""],
            [["share", "--format", "bar", "--unit", "percent"], "", 1, "", ""],
            [["topk", "--k", "3"], "", 1, "", ""],
            [["topp", "--p", "900"], "", 1, "", ""],
            [["minp", "--ratio", "0"], "", 1, "", ""],
            [["chain", "--steps", "topk:2,minp:100"], "", 1, "", ""],
            [["draw", "--at", "7"], "", 1, "", ""],
            [["share", "--format", "csv"], "", 1, "", ""],
            [[], "", 0, "", ""],
            [["help"], "", 0, "", ""],
            [["-h"], "", 0, "", ""],
            [["--help"], "", 0, "", ""],
            [["--version"], "", 0, "", ""],
            [["frob"], "", 0, "", ""],
            [["check", "--format", "plain"], "", 0, "", ""],
            [["share", "--unit", "x"], "", 0, "", ""],
            [["topk"], "", 0, "", ""],
            [["topk", "--k", "0"], "", 0, "", ""],
            [["draw", "--at", "x"], "", 0, "", ""],
            [["chain", "--steps", "topk:2,"], "", 0, "", ""],
            [["Check"], "", 0, "", ""],
        ];
    }
}
