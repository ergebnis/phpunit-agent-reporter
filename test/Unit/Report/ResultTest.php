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
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Report\Result::class)]
final class ResultTest extends Framework\TestCase
{
    public function testExceptionReturnsResultWithValueException(): void
    {
        $result = Report\Result::exception();

        self::assertSame('exception', $result->toString());
    }

    public function testFailureReturnsResultWithValueFailure(): void
    {
        $result = Report\Result::failure();

        self::assertSame('failure', $result->toString());
    }

    public function testSuccessReturnsResultWithValueSuccess(): void
    {
        $result = Report\Result::success();

        self::assertSame('success', $result->toString());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $one = Report\Result::success();
        $other = Report\Result::failure();

        self::assertFalse($one->equals($other));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $one = Report\Result::success();
        $other = Report\Result::success();

        self::assertTrue($one->equals($other));
    }
}
