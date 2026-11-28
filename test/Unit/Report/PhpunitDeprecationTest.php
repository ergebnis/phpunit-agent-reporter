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

#[Framework\Attributes\CoversClass(AgentReporter\Report\PhpunitDeprecation::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
final class PhpunitDeprecationTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsPhpunitDeprecationWhenNullableValuesAreNotNull(): void
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

        $phpunitDeprecation = AgentReporter\Report\PhpunitDeprecation::create(
            $testIdentifier,
            $file,
            $line,
            $message,
        );

        self::assertSame($testIdentifier, $phpunitDeprecation->testIdentifier());
        self::assertSame($file, $phpunitDeprecation->file());
        self::assertSame($line, $phpunitDeprecation->line());
        self::assertSame($message, $phpunitDeprecation->message());
    }

    public function testCreateReturnsPhpunitDeprecationWhenNullableValuesAreNull(): void
    {
        $faker = self::faker();

        $message = AgentReporter\Report\Message::fromString($faker->sentence());

        $phpunitDeprecation = AgentReporter\Report\PhpunitDeprecation::create(
            null,
            null,
            null,
            $message,
        );

        self::assertNull($phpunitDeprecation->testIdentifier());
        self::assertNull($phpunitDeprecation->file());
        self::assertNull($phpunitDeprecation->line());
        self::assertSame($message, $phpunitDeprecation->message());
    }
}
