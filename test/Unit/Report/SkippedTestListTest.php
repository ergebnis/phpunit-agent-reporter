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

#[Framework\Attributes\CoversClass(Report\SkippedTestList::class)]
#[Framework\Attributes\UsesClass(Report\Count::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\SkippedTest::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
final class SkippedTestListTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsSkippedTestListWhenValuesAreEmpty(): void
    {
        $skippedTestList = Report\SkippedTestList::create();

        self::assertSame([], $skippedTestList->toArray());
        self::assertEquals(Report\Count::zero(), $skippedTestList->count());
    }

    public function testCreateReturnsSkippedTestListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): Report\SkippedTest {
            return Report\SkippedTest::create(
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

        $skippedTestList = Report\SkippedTestList::create(...$values);

        self::assertSame($values, $skippedTestList->toArray());
        self::assertEquals(Report\Count::fromInt(\count($values)), $skippedTestList->count());
    }
}
