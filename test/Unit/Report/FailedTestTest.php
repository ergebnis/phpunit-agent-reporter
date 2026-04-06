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

#[Framework\Attributes\CoversClass(Report\FailedTest::class)]
#[Framework\Attributes\UsesClass(Report\Actual::class)]
#[Framework\Attributes\UsesClass(Report\ComparisonFailure::class)]
#[Framework\Attributes\UsesClass(Report\Diff::class)]
#[Framework\Attributes\UsesClass(Report\Expected::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
final class FailedTestTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsFailedTestWhenNullableValuesAreNotNull(): void
    {
        $faker = self::faker();

        $testIdentifier = Report\TestIdentifier::fromString(\sprintf(
            '%s::%s',
            $faker->word(),
            $faker->word(),
        ));
        $file = Report\File::fromString(\sprintf(
            '%s/%s.php',
            $faker->word(),
            $faker->word(),
        ));
        $line = Report\Line::fromInt($faker->numberBetween(1, 500));
        $message = Report\Message::fromString($faker->sentence());
        $comparisonFailure = Report\ComparisonFailure::create(
            Report\Actual::fromString($faker->text()),
            Report\Diff::fromString($faker->text()),
            Report\Expected::fromString($faker->text()),
        );

        $failedTest = Report\FailedTest::create(
            $testIdentifier,
            $file,
            $line,
            $message,
            $comparisonFailure,
        );

        self::assertSame($testIdentifier, $failedTest->testIdentifier());
        self::assertSame($file, $failedTest->file());
        self::assertSame($line, $failedTest->line());
        self::assertSame($message, $failedTest->message());
        self::assertSame($comparisonFailure, $failedTest->comparisonFailure());
    }

    public function testCreateReturnsFailedTestWhenNullableValuesAreNull(): void
    {
        $faker = self::faker();

        $testIdentifier = Report\TestIdentifier::fromString(\sprintf(
            '%s::%s',
            $faker->word(),
            $faker->word(),
        ));
        $file = Report\File::fromString(\sprintf(
            '%s/%s.php',
            $faker->word(),
            $faker->word(),
        ));
        $line = Report\Line::fromInt($faker->numberBetween(1, 500));
        $message = Report\Message::fromString($faker->sentence());

        $failedTest = Report\FailedTest::create(
            $testIdentifier,
            $file,
            $line,
            $message,
            null,
        );

        self::assertSame($testIdentifier, $failedTest->testIdentifier());
        self::assertSame($file, $failedTest->file());
        self::assertSame($line, $failedTest->line());
        self::assertSame($message, $failedTest->message());
        self::assertNull($failedTest->comparisonFailure());
    }
}
