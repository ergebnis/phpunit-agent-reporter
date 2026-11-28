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

#[Framework\Attributes\CoversClass(AgentReporter\Report\Count::class)]
final class CountTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testZeroReturnsCountWithValueZero(): void
    {
        $count = AgentReporter\Report\Count::zero();

        self::assertSame(0, $count->toInt());
    }

    public function testFromIntReturnsCount(): void
    {
        $value = self::faker()->numberBetween(0, 500);

        $count = AgentReporter\Report\Count::fromInt($value);

        self::assertSame($value, $count->toInt());
    }

    public function testEqualsReturnsFalseWhenValuesAreNotEqual(): void
    {
        $faker = self::faker();

        $one = AgentReporter\Report\Count::fromInt($faker->numberBetween(0, 249));
        $two = AgentReporter\Report\Count::fromInt($faker->numberBetween(250, 500));

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValuesAreEqual(): void
    {
        $value = self::faker()->numberBetween(0, 500);

        $one = AgentReporter\Report\Count::fromInt($value);
        $two = AgentReporter\Report\Count::fromInt($value);

        self::assertTrue($one->equals($two));
    }

    public function testIsGreaterThanReturnsFalseWhenValuesAreEqual(): void
    {
        $value = self::faker()->numberBetween(0, 500);

        $one = AgentReporter\Report\Count::fromInt($value);
        $two = AgentReporter\Report\Count::fromInt($value);

        self::assertFalse($one->isGreaterThan($two));
    }

    public function testIsGreaterThanReturnsFalseWhenValueIsLess(): void
    {
        $value = self::faker()->numberBetween(0, 500);

        $one = AgentReporter\Report\Count::fromInt($value - 1);
        $two = AgentReporter\Report\Count::fromInt($value);

        self::assertFalse($one->isGreaterThan($two));
    }

    public function testIsGreaterThanReturnsTrueWhenValueIsGreater(): void
    {
        $value = self::faker()->numberBetween(0, 500);

        $one = AgentReporter\Report\Count::fromInt($value + 1);
        $two = AgentReporter\Report\Count::fromInt($value);

        self::assertTrue($one->isGreaterThan($two));
    }

    public function testAddReturnsCount(): void
    {
        $faker = self::faker();

        $one = AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100));
        $two = AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100));

        $three = $one->add($two);

        self::assertNotSame($one, $three);
        self::assertNotSame($two, $three);
        self::assertSame($one->toInt() + $two->toInt(), $three->toInt());
    }

    public function testMinusReturnsCount(): void
    {
        $faker = self::faker();

        $one = AgentReporter\Report\Count::fromInt($faker->numberBetween(100, 200));
        $two = AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 99));

        $three = $one->minus($two);

        self::assertNotSame($one, $three);
        self::assertNotSame($two, $three);
        self::assertSame($one->toInt() - $two->toInt(), $three->toInt());
    }
}
