<p align="center"><img src="docs/logo.svg" width="96" alt="" /></p>

# cutpick

[![CI](https://github.com/Gulliby-development/cutpick/actions/workflows/ci.yml/badge.svg)](https://github.com/Gulliby-development/cutpick/actions/workflows/ci.yml) [![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

cutpick replays the cuts that a language-model sampler makes: top-k, top-p and min-p. It reads a short list of weighted candidates on standard input and shows what each cut keeps. It needs PHP 8.2 or newer and the standard library only. All arithmetic is on integers, and it uses no random numbers and no clock, so the same input always gives the same output.

It is for people who tune or debug samplers and want to replay a cut on a list they can read, in a terminal, in a script or in CI.

Run it from the repository directory:

```text
php bin/cutpick COMMAND
```

## Usage

```text
usage: cutpick check
       cutpick share [--unit UNIT] [--format FORMAT]
       cutpick topk --k N [--format FORMAT]
       cutpick topp --p PERMILLE [--format FORMAT]
       cutpick minp --ratio PERMILLE [--format FORMAT]
       cutpick chain --steps STEPS [--format FORMAT]
       cutpick draw --at N
```

Commands, in the order of the usage text:

1. `check` validates the list and prints `ok, N candidates, total T`. The count is of candidates only; skipped lines do not count.
2. `share` prints every candidate in input order with its share of the total weight.
3. `topk` keeps the N heaviest candidates.
4. `topp` keeps the smallest group of heaviest candidates that reaches a share P of the total.
5. `minp` keeps the candidates that weigh at least a ratio of the heaviest one.
6. `chain` applies several filters one after the other.
7. `draw` names the candidate that a position in the cumulative weights falls on.

Options and their values:

- `--format` is `plain`, `csv` or `bar`. The default is `plain`.
- `--unit` is `permille`, `percent` or `ppm`. The default is `permille`.
- `--k` is a whole number from 1 to 32.
- `--p` is a whole number from 1 to 1000.
- `--ratio` is a whole number from 0 to 1000.
- `--at` is a whole number from 0 to 999999999.
- `--steps` is one to four steps separated by commas, each `topk:N`, `topp:P` or `minp:R`, with a value in the range of the option of the same name, for example `topk:3,minp:100`.

Numbers have no sign, no leading zero and no more digits than the largest value has. Every option takes the next word as its value; there is no `--option=value` form. `share` accepts `--unit` and `--format`; `topk`, `topp`, `minp` and `chain` accept their own option and `--format`; `draw` accepts `--at`; `check` accepts none. `topk`, `topp`, `minp`, `chain` and `draw` require their own option.

`help`, `-h` and `--help` as the first word print the usage on standard output and exit 0. `--version` prints `cutpick 0.1.0` and exits 0. With no word the usage goes to standard error and the exit status is 2.

## List format

One candidate per line: `TOKEN WEIGHT`, separated by spaces or tabs. Spaces and tabs at the ends of a line are ignored.

- A token is 1 to 12 characters from `A-Z`, `a-z`, `0-9`, `_`, `.` and `-`. Upper and lower case are different tokens.
- A weight is `0`, or 1 to 6 decimal digits without a leading zero and without a sign (0 to 999999).
- Tokens must be unique.
- Blank lines and lines whose first word starts with `#` are skipped.
- A CR at the end of a line is dropped.
- At most 32 lines are allowed, skipped lines included.
- There must be at least one candidate and the total weight must not be zero.

## Formats

A table has one line per candidate, in one of three formats:

- `plain`: `TOKEN NUMBER`
- `csv`: `TOKEN,NUMBER`
- `bar`: `TOKEN NUMBER MARKS`, where MARKS is a run of `#` characters. A number of 0 has no marks, so its line ends with a space.

The marks are `NUMBER * 20 / FULL` rounded down, at least one mark for a number above 0, and at most 20. FULL is the number that stands for a full bar: the unit's scale for `share` (1000, 100 or 1000000) and the heaviest weight of the table for the other commands.

The ranking orders candidates by weight, heaviest first. Candidates with the same weight keep their input order.

`share` prints `intdiv(W * SCALE + intdiv(T, 2), T)` for each candidate, where W is its weight, T the total and SCALE the unit's scale. A half rounds up, so the shares need not add up to the scale.

## Filters

- `topk --k N` keeps the N heaviest candidates, in ranking order (all of them when the list is shorter).
- `topp --p P` walks down the ranking and keeps every candidate up to and including the first one at which the weight kept so far times 1000 is at least P times the total weight.
- `minp --ratio R` keeps the candidates whose weight times 1000 is at least R times the heaviest weight. The heaviest one always stays.
- `chain --steps STEPS` applies the steps one after the other, each to the candidates the step before it kept (the first one to the whole list). A `topp` step uses the total weight of the candidates that are left, and a `minp` step uses the heaviest weight of the candidates that are left. Weights never change. The output is the table of what is left, in ranking order.
- `draw --at N` walks the cumulative weights in input order (not ranking order) and prints the token of the first candidate whose running total after its own weight is above N. A position of the total or more is an error.

## Examples

In the examples, `cutpick` stands for a shell function that runs `php bin/cutpick "$@"`. They use a POSIX shell in the repository directory; `2>&1` shows the error line.

```text
$ printf 'the 500\ncat 300\ndog 150\nsat 40\nmat 10\n' | cutpick topp --p 900
the 500
cat 300
dog 150
$ printf 'the 500\ncat 300\ndog 150\n' | cutpick share --unit percent --format bar; printf 'a 1\nb 3\n' | cutpick share --format csv
the 53 ##########
cat 32 ######
dog 16 ###
a,250
b,750
$ printf 'the 500\ncat 300\ndog 150\nsat 40\nmat 10\n' | cutpick chain --steps topk:4,minp:100; printf 'the 500\ncat 300\ndog 150\n' | cutpick draw --at 520
the 500
cat 300
dog 150
cat
$ printf 'a 1\nb\n' | cutpick check 2>&1; printf 'a 1\n' | cutpick topk 2>&1
cutpick: line 2: expected TOKEN WEIGHT
cutpick: missing --k
```

## Errors

A failure writes nothing to standard output and one line `cutpick: MESSAGE` to standard error.

Command-line errors:

- `unexpected argument WORD` (a word that does not start with `-`, or is `-` or empty)
- `unknown option WORD`
- `option OPTION is not valid for COMMAND`
- `given twice: OPTION`
- `missing value for OPTION`
- `bad value for OPTION`
- `missing --k`, `missing --p`, `missing --ratio`, `missing --steps`, `missing --at`
- `unknown command WORD`

List errors:

- `input is too long (max 32 lines)`
- `line N: expected TOKEN WEIGHT`
- `line N: bad token WORD`
- `line N: bad weight WORD`
- `line N: duplicate token WORD`
- `no candidates`
- `total weight is zero`

Command error:

- `draw position N is outside the total T`

Exit statuses:

- 0: success, including help and version.
- 1: a list error or a command error.
- 2: a command-line error, or no command word.

Checks run in this order: the command line (the input is not read), then the list, then the command.

## Development

```text
php vendor/bin/pint --test
php vendor/bin/phpstan analyse --no-progress
php vendor/bin/phpunit
```

---

Built with [BAKED](https://github.com/Gulliby-development/baked), an open-source coding agent (bench round R105).
