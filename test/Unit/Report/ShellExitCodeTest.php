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

namespace Ergebnis\PHPUnit\AgentReporter\Test\Unit\Report;

use Ergebnis\PHPUnit\AgentReporter\Report;
use Ergebnis\PHPUnit\AgentReporter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Report\ShellExitCode::class)]
final class ShellExitCodeTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromIntReturnsShellExitCode(): void
    {
        $faker = self::faker();

        $value = $faker->numberBetween(0, 255);

        $shellExitCode = Report\ShellExitCode::fromInt($value);

        self::assertSame($value, $shellExitCode->toInt());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $one = Report\ShellExitCode::fromInt(0);
        $other = Report\ShellExitCode::fromInt(1);

        self::assertFalse($one->equals($other));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = 42;

        $one = Report\ShellExitCode::fromInt($value);
        $other = Report\ShellExitCode::fromInt($value);

        self::assertTrue($one->equals($other));
    }

    public function testExceptionReturnsShellExitCodeWithValueTwo(): void
    {
        $shellExitCode = Report\ShellExitCode::exception();

        self::assertSame(2, $shellExitCode->toInt());
    }

    public function testFailureReturnsShellExitCodeWithValueOne(): void
    {
        $shellExitCode = Report\ShellExitCode::failure();

        self::assertSame(1, $shellExitCode->toInt());
    }

    public function testSuccessReturnsShellExitCodeWithValueZero(): void
    {
        $shellExitCode = Report\ShellExitCode::success();

        self::assertSame(0, $shellExitCode->toInt());
    }
}
