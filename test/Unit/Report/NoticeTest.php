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

#[Framework\Attributes\CoversClass(Report\Notice::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifierList::class)]
final class NoticeTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsNotice(): void
    {
        $faker = self::faker();

        $file = Report\File::fromString(\sprintf(
            '%s/%s.php',
            $faker->word(),
            $faker->word(),
        ));
        $line = Report\Line::fromInt($faker->numberBetween(1, 500));
        $message = Report\Message::fromString($faker->sentence());
        $triggeredBy = Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
            return Report\TestIdentifier::fromString(\sprintf(
                '%s::%s',
                $faker->word(),
                $faker->word(),
            ));
        }, \range(0, 2)));

        $notice = Report\Notice::create(
            $file,
            $line,
            $message,
            $triggeredBy,
        );

        self::assertSame($file, $notice->file());
        self::assertSame($line, $notice->line());
        self::assertSame($message, $notice->message());
        self::assertSame($triggeredBy, $notice->triggeredBy());
    }
}
