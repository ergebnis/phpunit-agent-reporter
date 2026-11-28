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

#[Framework\Attributes\CoversClass(AgentReporter\Report\Warning::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifierList::class)]
final class WarningTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsWarning(): void
    {
        $faker = self::faker();

        $file = AgentReporter\Report\File::fromString(\sprintf(
            '%s/%s.php',
            $faker->word(),
            $faker->word(),
        ));
        $line = AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500));
        $message = AgentReporter\Report\Message::fromString($faker->sentence());
        $triggeredBy = AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
            return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                '%s::%s',
                $faker->word(),
                $faker->word(),
            ));
        }, \range(0, 2)));

        $warning = AgentReporter\Report\Warning::create(
            $file,
            $line,
            $message,
            $triggeredBy,
        );

        self::assertSame($file, $warning->file());
        self::assertSame($line, $warning->line());
        self::assertSame($message, $warning->message());
        self::assertSame($triggeredBy, $warning->triggeredBy());
    }
}
