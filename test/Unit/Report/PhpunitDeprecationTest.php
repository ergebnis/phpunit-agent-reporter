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

#[Framework\Attributes\CoversClass(Report\PhpunitDeprecation::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
final class PhpunitDeprecationTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsPhpunitDeprecationWhenNullableValuesAreNotNull(): void
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

        $phpunitDeprecation = Report\PhpunitDeprecation::create(
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

        $message = Report\Message::fromString($faker->sentence());

        $phpunitDeprecation = Report\PhpunitDeprecation::create(
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
