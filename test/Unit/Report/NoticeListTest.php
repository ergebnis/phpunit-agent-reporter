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

#[Framework\Attributes\CoversClass(Report\NoticeList::class)]
#[Framework\Attributes\UsesClass(Report\Count::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\Notice::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifierList::class)]
final class NoticeListTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsNoticeListWhenValuesAreEmpty(): void
    {
        $noticeList = Report\NoticeList::create();

        self::assertSame([], $noticeList->toArray());
        self::assertEquals(Report\Count::zero(), $noticeList->count());
    }

    public function testCreateReturnsNoticeListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): Report\Notice {
            return Report\Notice::create(
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
                Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                    return Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    ));
                }, \range(0, 2))),
            );
        }, \range(0, $faker->numberBetween(1, 9)));

        $noticeList = Report\NoticeList::create(...$values);

        self::assertSame($values, $noticeList->toArray());
        self::assertEquals(Report\Count::fromInt(\count($values)), $noticeList->count());
    }
}
