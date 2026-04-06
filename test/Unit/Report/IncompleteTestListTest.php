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

#[Framework\Attributes\CoversClass(Report\IncompleteTestList::class)]
#[Framework\Attributes\UsesClass(Report\Count::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\IncompleteTest::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
final class IncompleteTestListTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsIncompleteTestListWhenValuesAreEmpty(): void
    {
        $incompleteTestList = Report\IncompleteTestList::create();

        self::assertSame([], $incompleteTestList->toArray());
        self::assertEquals(Report\Count::zero(), $incompleteTestList->count());
    }

    public function testCreateReturnsIncompleteTestListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): Report\IncompleteTest {
            return Report\IncompleteTest::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, $faker->numberBetween(1, 9)));

        $incompleteTestList = Report\IncompleteTestList::create(...$values);

        self::assertSame($values, $incompleteTestList->toArray());
        self::assertEquals(Report\Count::fromInt(\count($values)), $incompleteTestList->count());
    }
}
