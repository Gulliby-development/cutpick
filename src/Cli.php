<?php

declare(strict_types=1);

namespace Cutpick;

use Cutpick\Commands\ChainCommand;
use Cutpick\Commands\CheckCommand;
use Cutpick\Commands\DrawCommand;
use Cutpick\Commands\MinpCommand;
use Cutpick\Commands\ShareCommand;
use Cutpick\Commands\TopkCommand;
use Cutpick\Commands\ToppCommand;

/**
 * The command line: help, version, option rules, the reading of the list and the call of the command.
 *
 * @phpstan-import-type Options from Registry
 * @phpstan-import-type Reply from Registry
 */
final class Cli
{
    /**
     * @param list<string> $args
     * @return Reply
     */
    public static function run(array $args, string $input): array
    {
        if ($args === []) {
            return ['status' => 2, 'out' => '', 'err' => Registry::USAGE];
        }
        $first = $args[0];
        if ($first === 'help' || $first === '-h' || $first === '--help') {
            return ['status' => 0, 'out' => Registry::USAGE, 'err' => ''];
        }
        if ($first === '--version') {
            return ['status' => 0, 'out' => Registry::VERSION . "\n", 'err' => ''];
        }

        try {
            $entry = self::find($first);
            if ($entry === null) {
                throw new Failure(2, 'unknown command ' . $first);
            }
            $options = self::parse($entry, array_slice($args, 1));
            $items = Input::read($input);

            return match ($first) {
                'check' => CheckCommand::run($options, $items),
                'share' => ShareCommand::run($options, $items),
                'topk' => TopkCommand::run($options, $items),
                'topp' => ToppCommand::run($options, $items),
                'minp' => MinpCommand::run($options, $items),
                'chain' => ChainCommand::run($options, $items),
                default => DrawCommand::run($options, $items),
            };
        } catch (Failure $e) {
            return ['status' => $e->status(), 'out' => '', 'err' => 'cutpick: ' . $e->getMessage() . "\n"];
        }
    }

    /**
     * @param list<string> $args
     */
    public static function readsStdin(array $args): bool
    {
        if ($args === []) {
            return false;
        }
        $entry = self::find($args[0]);
        if ($entry === null) {
            return false;
        }

        return !(self::attempt($entry, array_slice($args, 1)) instanceof Failure);
    }

    /**
     * @param array{name: string, options: list<string>, required: string} $entry
     * @param list<string> $words
     * @return Options|Failure
     */
    private static function attempt(array $entry, array $words): array|Failure
    {
        try {
            return self::parse($entry, $words);
        } catch (Failure $failure) {
            return $failure;
        }
    }

    /**
     * @return array{name: string, options: list<string>, required: string}|null
     */
    private static function find(string $word): ?array
    {
        foreach (Registry::COMMANDS as $entry) {
            if ($entry['name'] === $word) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * @param array{name: string, options: list<string>, required: string} $entry
     * @param list<string> $words
     * @return Options
     */
    private static function parse(array $entry, array $words): array
    {
        $format = 'plain';
        $unit = 'permille';
        $k = null;
        $p = null;
        $ratio = null;
        $steps = null;
        $at = null;
        $seen = [];
        $count = count($words);

        for ($i = 0; $i < $count; $i++) {
            $word = $words[$i];
            if ($word === '' || $word === '-' || $word[0] !== '-') {
                throw new Failure(2, 'unexpected argument ' . $word);
            }
            if (!in_array($word, Registry::OPTION_NAMES, true)) {
                throw new Failure(2, 'unknown option ' . $word);
            }
            if (!in_array($word, $entry['options'], true)) {
                throw new Failure(2, 'option ' . $word . ' is not valid for ' . $entry['name']);
            }
            if (in_array($word, $seen, true)) {
                throw new Failure(2, 'given twice: ' . $word);
            }
            $seen[] = $word;
            if ($i + 1 >= $count) {
                throw new Failure(2, 'missing value for ' . $word);
            }
            $i++;
            $value = $words[$i];
            $bad = new Failure(2, 'bad value for ' . $word);

            switch ($word) {
                case '--format':
                    if (!in_array($value, Registry::FORMATS, true)) {
                        throw $bad;
                    }
                    $format = $value;
                    break;
                case '--unit':
                    if (!array_key_exists($value, Registry::UNITS)) {
                        throw $bad;
                    }
                    $unit = $value;
                    break;
                case '--k':
                    $k = self::number($value, 1, 32) ?? throw $bad;
                    break;
                case '--p':
                    $p = self::number($value, 1, 1000) ?? throw $bad;
                    break;
                case '--ratio':
                    $ratio = self::number($value, 0, 1000) ?? throw $bad;
                    break;
                case '--at':
                    $at = self::number($value, 0, 999999999) ?? throw $bad;
                    break;
                default:
                    if (!self::stepsValid($value)) {
                        throw $bad;
                    }
                    $steps = $value;
                    break;
            }
        }

        if ($entry['required'] !== '' && !in_array($entry['required'], $seen, true)) {
            throw new Failure(2, 'missing ' . $entry['required']);
        }

        return [
            'format' => $format,
            'unit' => $unit,
            'k' => $k,
            'p' => $p,
            'ratio' => $ratio,
            'steps' => $steps,
            'at' => $at,
        ];
    }

    private static function number(string $value, int $min, int $max): ?int
    {
        if (preg_match('/^(0|[1-9][0-9]*)\z/', $value) !== 1) {
            return null;
        }
        if (strlen($value) > strlen((string) $max)) {
            return null;
        }
        $number = (int) $value;

        return $number >= $min && $number <= $max ? $number : null;
    }

    private static function stepsValid(string $value): bool
    {
        $parts = explode(',', $value);
        if (count($parts) > 4) {
            return false;
        }
        foreach ($parts as $part) {
            if (preg_match('/^(topk|topp|minp):(.*)\z/s', $part, $m) !== 1) {
                return false;
            }
            $ok = match ($m[1]) {
                'topk' => self::number($m[2], 1, 32),
                'topp' => self::number($m[2], 1, 1000),
                default => self::number($m[2], 0, 1000),
            };
            if ($ok === null) {
                return false;
            }
        }

        return true;
    }
}
