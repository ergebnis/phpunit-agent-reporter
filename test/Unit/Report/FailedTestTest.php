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

use Ergebnis\PHPUnit\AgentReporter;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(AgentReporter\Report\FailedTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Actual::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ComparisonFailure::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Diff::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Expected::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
final class FailedTestTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsFailedTestWhenNullableValuesAreNotNull(): void
    {
        $faker = self::faker();

        $testIdentifier = AgentReporter\Report\TestIdentifier::fromString(\sprintf(
            '%s::%s',
            $faker->word(),
            $faker->word(),
        ));
        $file = AgentReporter\Report\File::fromString(\sprintf(
            '%s/%s.php',
            $faker->word(),
            $faker->word(),
        ));
        $line = AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500));
        $message = AgentReporter\Report\Message::fromString($faker->sentence());
        $comparisonFailure = AgentReporter\Report\ComparisonFailure::create(
            AgentReporter\Report\Actual::fromString($faker->text()),
            AgentReporter\Report\Diff::fromString($faker->text()),
            AgentReporter\Report\Expected::fromString($faker->text()),
        );

        $failedTest = AgentReporter\Report\FailedTest::create(
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

        $testIdentifier = AgentReporter\Report\TestIdentifier::fromString(\sprintf(
            '%s::%s',
            $faker->word(),
            $faker->word(),
        ));
        $file = AgentReporter\Report\File::fromString(\sprintf(
            '%s/%s.php',
            $faker->word(),
            $faker->word(),
        ));
        $line = AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500));
        $message = AgentReporter\Report\Message::fromString($faker->sentence());

        $failedTest = AgentReporter\Report\FailedTest::create(
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
