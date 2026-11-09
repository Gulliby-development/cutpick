<?php

declare(strict_types=1);

namespace Cutpick;

/**
 * The version, the limit, the usage text, the formats, the units, the options, the command table and the shared types.
 *
 * @phpstan-type Item array{token: string, weight: int}
 * @phpstan-type Options array{format: string, unit: string, k: int|null, p: int|null, ratio: int|null, steps: string|null, at: int|null}
 * @phpstan-type Reply array{status: int, out: string, err: string}
 * @phpstan-type Row array{token: string, number: int, full: int}
 */
final class Registry
{
    public const VERSION = 'cutpick 0.1.0';
    public const MAX_LINES = 32;

    public const USAGE = "usage: cutpick check\n"
        . "       cutpick share [--unit UNIT] [--format FORMAT]\n"
        . "       cutpick topk --k N [--format FORMAT]\n"
        . "       cutpick topp --p PERMILLE [--format FORMAT]\n"
        . "       cutpick minp --ratio PERMILLE [--format FORMAT]\n"
        . "       cutpick chain --steps STEPS [--format FORMAT]\n"
        . "       cutpick draw --at N\n";

    public const FORMATS = ['plain', 'csv', 'bar'];

    public const UNITS = ['permille' => 1000, 'percent' => 100, 'ppm' => 1000000];

    public const OPTION_NAMES = ['--format', '--unit', '--k', '--p', '--ratio', '--steps', '--at'];

    /**
     * One entry per command, in the order of the usage text: its word, the options it accepts and the option that must be given (or '').
     * The class Commands\NAMECommand (NAME with a capital first letter) has the function run(options, items).
     *
     * @var list<array{name: string, options: list<string>, required: string}>
     */
    public const COMMANDS = [
        ['name' => 'check', 'options' => [], 'required' => ''],
        ['name' => 'share', 'options' => ['--unit', '--format'], 'required' => ''],
        ['name' => 'topk', 'options' => ['--k', '--format'], 'required' => '--k'],
        ['name' => 'topp', 'options' => ['--p', '--format'], 'required' => '--p'],
        ['name' => 'minp', 'options' => ['--ratio', '--format'], 'required' => '--ratio'],
        ['name' => 'chain', 'options' => ['--steps', '--format'], 'required' => '--steps'],
        ['name' => 'draw', 'options' => ['--at'], 'required' => '--at'],
    ];
}
