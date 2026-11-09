<?php

declare(strict_types=1);

namespace Cutpick\Tests;

use Cutpick\Cli;
use PHPUnit\Framework\Assert;

/**
 * The helper of the row files: a row is [args, input, status, out, err] or, for assertReads, [args, '', reads, '', ''].
 */
final class Harness
{
    /**
     * @param list<string> $args
     */
    public static function assertReply(array $args, string $input, int $status, string $out, string $err): void
    {
        $reply = Cli::run($args, $input);
        Assert::assertSame([$status, $out, $err], [$reply['status'], $reply['out'], $reply['err']]);
    }

    /**
     * @param list<string> $args
     */
    public static function assertReads(array $args, int $reads): void
    {
        Assert::assertSame($reads === 1, Cli::readsStdin($args));
    }
}
