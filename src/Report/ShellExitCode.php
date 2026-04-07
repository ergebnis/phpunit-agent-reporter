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
final class ShellExitCode
{
    private readonly int $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @see https://github.com/sebastianbergmann/phpunit/blob/10.0.0/src/TextUI/ShellExitCodeCalculator.php#L23
     */
    public static function exception(): self
    {
        return new self(2);
    }

    /**
     * @see https://github.com/sebastianbergmann/phpunit/blob/10.0.0/src/TextUI/ShellExitCodeCalculator.php#L21
     */
    public static function failure(): self
    {
        return new self(1);
    }

    /**
     * @see https://github.com/sebastianbergmann/phpunit/blob/10.0.0/src/TextUI/ShellExitCodeCalculator.php#L19
     */
    public static function success(): self
    {
        return new self(0);
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
