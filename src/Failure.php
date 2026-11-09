<?php

declare(strict_types=1);

namespace Cutpick;

use RuntimeException;

/**
 * A failure that ends the command: status 1 for the list and for a command, 2 for the command line.
 * Cli::run turns it into `cutpick: MESSAGE` on standard error.
 */
final class Failure extends RuntimeException
{
    public function __construct(private readonly int $status, string $message)
    {
        parent::__construct($message);
    }

    public function status(): int
    {
        return $this->status;
    }
}
