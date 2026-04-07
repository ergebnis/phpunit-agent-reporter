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

#[Framework\Attributes\CoversClass(Report\FailedTestList::class)]
#[Framework\Attributes\UsesClass(Report\Count::class)]
#[Framework\Attributes\UsesClass(Report\FailedTest::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(Report\Actual::class)]
#[Framework\Attributes\UsesClass(Report\Expected::class)]
#[Framework\Attributes\UsesClass(Report\Diff::class)]
#[Framework\Attributes\UsesClass(Report\ComparisonFailure::class)]
final class FailedTestListTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsFailedTestListWhenValuesAreEmpty(): void
    {
        $failedTestList = Report\FailedTestList::create();

        self::assertSame([], $failedTestList->toArray());
        self::assertEquals(Report\Count::zero(), $failedTestList->count());
    }

    public function testCreateReturnsFailedTestListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): Report\FailedTest {
            return Report\FailedTest::create(
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
                Report\ComparisonFailure::create(
                    Report\Actual::fromString($faker->text()),
                    Report\Diff::fromString($faker->text()),
                    Report\Expected::fromString($faker->text()),
                ),
            );
        }, \range(0, $faker->numberBetween(1, 9)));

        $failedTestList = Report\FailedTestList::create(...$values);

        self::assertSame($values, $failedTestList->toArray());
        self::assertEquals(Report\Count::fromInt(\count($values)), $failedTestList->count());
    }
}
