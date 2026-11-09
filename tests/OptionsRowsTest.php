<?php

declare(strict_types=1);

namespace Cutpick\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OptionsRowsTest extends TestCase
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
            [["share", "extra"], "", 2, "", "cutpick: unexpected argument extra\n"],
            [["share", "-"], "", 2, "", "cutpick: unexpected argument -\n"],
            [["share", ""], "", 2, "", "cutpick: unexpected argument \n"],
            [["share", "--bogus"], "", 2, "", "cutpick: unknown option --bogus\n"],
            [["check", "--format", "plain"], "", 2, "", "cutpick: option --format is not valid for check\n"],
            [["share", "--k", "3"], "", 2, "", "cutpick: option --k is not valid for share\n"],
            [["share", "--unit", "ppm", "--unit", "ppm"], "", 2, "", "cutpick: given twice: --unit\n"],
            [["share", "--unit"], "", 2, "", "cutpick: missing value for --unit\n"],
            [["share", "-x", "--unit"], "", 2, "", "cutpick: unknown option -x\n"],
            [["check", "--unit"], "", 2, "", "cutpick: option --unit is not valid for check\n"],
            [["share", "--unit", "x", "--bogus"], "", 2, "", "cutpick: bad value for --unit\n"],
            [["topk"], "", 2, "", "cutpick: missing --k\n"],
            [["topp"], "", 2, "", "cutpick: missing --p\n"],
            [["minp"], "", 2, "", "cutpick: missing --ratio\n"],
            [["chain"], "", 2, "", "cutpick: missing --steps\n"],
            [["draw"], "", 2, "", "cutpick: missing --at\n"],
            [["draw", "--format", "csv"], "", 2, "", "cutpick: option --format is not valid for draw\n"],
            [["topk", "--unit", "ppm", "--k", "2"], "", 2, "", "cutpick: option --unit is not valid for topk\n"],
            [["share", "--format", "csv", "--unit", "ppm"], "a 1\n", 0, "a,1000000\n", ""],
            [["share", "--unit", "percent", "--format", "plain"], "a 1\nb 1\n", 0, "a 50\nb 50\n", ""],
            [["topk", "--format", "csv", "--k", "1"], "the 500\ncat 300\ndog 150\nsat 40\nmat 10\n", 0, "the,500\n", ""],
            [["minp", "--format", "bar", "--ratio", "500"], "the 500\ncat 300\ndog 150\nsat 40\nmat 10\n", 0, "the 500 ####################\ncat 300 ############\n", ""],
            [["draw", "--at", "0"], "a 1\n", 0, "a\n", ""],
            [["chain", "--format", "csv", "--steps", "topk:1"], "the 500\ncat 300\ndog 150\nsat 40\nmat 10\n", 0, "the,500\n", ""],
        ];
    }
}
