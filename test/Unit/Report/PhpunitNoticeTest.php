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

#[Framework\Attributes\CoversClass(AgentReporter\Report\PhpunitNotice::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
final class PhpunitNoticeTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsPhpunitNoticeWhenNullableValuesAreNotNull(): void
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

        $phpunitNotice = AgentReporter\Report\PhpunitNotice::create(
            $testIdentifier,
            $file,
            $line,
            $message,
        );

        self::assertSame($testIdentifier, $phpunitNotice->testIdentifier());
        self::assertSame($file, $phpunitNotice->file());
        self::assertSame($line, $phpunitNotice->line());
        self::assertSame($message, $phpunitNotice->message());
    }

    public function testCreateReturnsPhpunitNoticeWhenNullableValuesAreNull(): void
    {
        $faker = self::faker();

        $message = AgentReporter\Report\Message::fromString($faker->sentence());

        $phpunitNotice = AgentReporter\Report\PhpunitNotice::create(
            null,
            null,
            null,
            $message,
        );

        self::assertNull($phpunitNotice->testIdentifier());
        self::assertNull($phpunitNotice->file());
        self::assertNull($phpunitNotice->line());
        self::assertSame($message, $phpunitNotice->message());
    }
}
