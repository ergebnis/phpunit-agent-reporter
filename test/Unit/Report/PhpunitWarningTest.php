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

#[Framework\Attributes\CoversClass(AgentReporter\Report\PhpunitWarning::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
final class PhpunitWarningTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsPhpunitWarningWhenNullableValuesAreNotNull(): void
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

        $phpunitWarning = AgentReporter\Report\PhpunitWarning::create(
            $testIdentifier,
            $file,
            $line,
            $message,
        );

        self::assertSame($testIdentifier, $phpunitWarning->testIdentifier());
        self::assertSame($file, $phpunitWarning->file());
        self::assertSame($line, $phpunitWarning->line());
        self::assertSame($message, $phpunitWarning->message());
    }

    public function testCreateReturnsPhpunitWarningWhenNullableValuesAreNull(): void
    {
        $faker = self::faker();

        $message = AgentReporter\Report\Message::fromString($faker->sentence());

        $phpunitWarning = AgentReporter\Report\PhpunitWarning::create(
            null,
            null,
            null,
            $message,
        );

        self::assertNull($phpunitWarning->testIdentifier());
        self::assertNull($phpunitWarning->file());
        self::assertNull($phpunitWarning->line());
        self::assertSame($message, $phpunitWarning->message());
    }
}
