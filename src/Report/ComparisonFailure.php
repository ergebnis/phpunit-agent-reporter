<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/phpunit-agent-reporter
 */

namespace Ergebnis\PHPUnit\AgentReporter\Report;

/**
 * @internal
 */
final class ComparisonFailure
{
    private readonly Actual $actual;
    private readonly Diff $diff;
    private readonly Expected $expected;

    private function __construct(
        Actual $actual,
        Diff $diff,
        Expected $expected,
    ) {
        $this->actual = $actual;
        $this->diff = $diff;
        $this->expected = $expected;
    }

    public static function create(
        Actual $actual,
        Diff $diff,
        Expected $expected,
    ): self {
        return new self(
            $actual,
            $diff,
            $expected,
        );
    }

    public function actual(): Actual
    {
        return $this->actual;
    }

    public function diff(): Diff
    {
        return $this->diff;
    }

    public function expected(): Expected
    {
        return $this->expected;
    }
}
