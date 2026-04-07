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

#[Framework\Attributes\CoversClass(Report\SkippedTest::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
final class SkippedTestTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsSkipped(): void
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

        $skippedTest = Report\SkippedTest::create(
            $testIdentifier,
            $file,
            $line,
            $message,
        );

        self::assertSame($testIdentifier, $skippedTest->testIdentifier());
        self::assertSame($file, $skippedTest->file());
        self::assertSame($line, $skippedTest->line());
        self::assertSame($message, $skippedTest->message());
    }
}
