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

#[Framework\Attributes\CoversClass(AgentReporter\Report\NoticeList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Count::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Notice::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifierList::class)]
final class NoticeListTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsNoticeListWhenValuesAreEmpty(): void
    {
        $noticeList = AgentReporter\Report\NoticeList::create();

        self::assertSame([], $noticeList->toArray());
        self::assertEquals(AgentReporter\Report\Count::zero(), $noticeList->count());
    }

    public function testCreateReturnsNoticeListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): AgentReporter\Report\Notice {
            return AgentReporter\Report\Notice::create(
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
                AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                    return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    ));
                }, \range(0, 2))),
            );
        }, \range(0, $faker->numberBetween(1, 9)));

        $noticeList = AgentReporter\Report\NoticeList::create(...$values);

        self::assertSame($values, $noticeList->toArray());
        self::assertEquals(AgentReporter\Report\Count::fromInt(\count($values)), $noticeList->count());
    }
}
