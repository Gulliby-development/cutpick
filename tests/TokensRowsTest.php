<?php

declare(strict_types=1);

namespace Cutpick\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TokensRowsTest extends TestCase
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
            [["check"], "a 1\n", 0, "ok, 1 candidate, total 1\n", ""],
            [["check"], "abcdefghijkl 1\n", 0, "ok, 1 candidate, total 1\n", ""],
            [["check"], "abcdefghijklm 1\n", 1, "", "cutpick: line 1: bad token abcdefghijklm\n"],
            [["check"], "A.b_c-D9 1\n", 0, "ok, 1 candidate, total 1\n", ""],
            [["check"], "a:b 1\n", 1, "", "cutpick: line 1: bad token a:b\n"],
            [["check"], "a/b 1\n", 1, "", "cutpick: line 1: bad token a/b\n"],
            [["check"], "caf? 1\n", 1, "", "cutpick: line 1: bad token caf?\n"],
            [["check"], "a 0\n", 1, "", "cutpick: total weight is zero\n"],
            [["check"], "a 999999\n", 0, "ok, 1 candidate, total 999999\n", ""],
            [["check"], "a 1000000\n", 1, "", "cutpick: line 1: bad weight 1000000\n"],
            [["check"], "a 01\n", 1, "", "cutpick: line 1: bad weight 01\n"],
            [["check"], "a -1\n", 1, "", "cutpick: line 1: bad weight -1\n"],
            [["check"], "a +1\n", 1, "", "cutpick: line 1: bad weight +1\n"],
            [["check"], "a 1.5\n", 1, "", "cutpick: line 1: bad weight 1.5\n"],
            [["check"], "a 1e3\n", 1, "", "cutpick: line 1: bad weight 1e3\n"],
            [["check"], "a 1\na 2\n", 1, "", "cutpick: line 2: duplicate token a\n"],
            [["check"], "a 1\nA 2\n", 0, "ok, 2 candidates, total 3\n", ""],
            [["check"], "a 1\nb 2\na 3\n", 1, "", "cutpick: line 3: duplicate token a\n"],
            [["check"], "a x\n", 1, "", "cutpick: line 1: bad weight x\n"],
            [["check"], "a 0x10\n", 1, "", "cutpick: line 1: bad weight 0x10\n"],
        ];
    }
}
