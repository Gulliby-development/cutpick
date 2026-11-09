<?php

declare(strict_types=1);

namespace Cutpick\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UsageRowsTest extends TestCase
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
            [["help"], "", 0, "usage: cutpick check\n       cutpick share [--unit UNIT] [--format FORMAT]\n       cutpick topk --k N [--format FORMAT]\n       cutpick topp --p PERMILLE [--format FORMAT]\n       cutpick minp --ratio PERMILLE [--format FORMAT]\n       cutpick chain --steps STEPS [--format FORMAT]\n       cutpick draw --at N\n", ""],
            [["-h"], "", 0, "usage: cutpick check\n       cutpick share [--unit UNIT] [--format FORMAT]\n       cutpick topk --k N [--format FORMAT]\n       cutpick topp --p PERMILLE [--format FORMAT]\n       cutpick minp --ratio PERMILLE [--format FORMAT]\n       cutpick chain --steps STEPS [--format FORMAT]\n       cutpick draw --at N\n", ""],
            [["--help", "--bogus"], "", 0, "usage: cutpick check\n       cutpick share [--unit UNIT] [--format FORMAT]\n       cutpick topk --k N [--format FORMAT]\n       cutpick topp --p PERMILLE [--format FORMAT]\n       cutpick minp --ratio PERMILLE [--format FORMAT]\n       cutpick chain --steps STEPS [--format FORMAT]\n       cutpick draw --at N\n", ""],
            [[], "", 2, "", "usage: cutpick check\n       cutpick share [--unit UNIT] [--format FORMAT]\n       cutpick topk --k N [--format FORMAT]\n       cutpick topp --p PERMILLE [--format FORMAT]\n       cutpick minp --ratio PERMILLE [--format FORMAT]\n       cutpick chain --steps STEPS [--format FORMAT]\n       cutpick draw --at N\n"],
            [["--version"], "", 0, "cutpick 0.1.0\n", ""],
            [["--version", "x"], "", 0, "cutpick 0.1.0\n", ""],
            [["frob"], "", 2, "", "cutpick: unknown command frob\n"],
            [[""], "", 2, "", "cutpick: unknown command \n"],
            [["Check"], "", 2, "", "cutpick: unknown command Check\n"],
            [["-H"], "", 2, "", "cutpick: unknown command -H\n"],
        ];
    }
}
