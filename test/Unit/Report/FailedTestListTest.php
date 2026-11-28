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

#[Framework\Attributes\CoversClass(AgentReporter\Report\FailedTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Count::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\FailedTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Actual::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Expected::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Diff::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ComparisonFailure::class)]
final class FailedTestListTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsFailedTestListWhenValuesAreEmpty(): void
    {
        $failedTestList = AgentReporter\Report\FailedTestList::create();

        self::assertSame([], $failedTestList->toArray());
        self::assertEquals(AgentReporter\Report\Count::zero(), $failedTestList->count());
    }

    public function testCreateReturnsFailedTestListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): AgentReporter\Report\FailedTest {
            return AgentReporter\Report\FailedTest::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
                AgentReporter\Report\ComparisonFailure::create(
                    AgentReporter\Report\Actual::fromString($faker->text()),
                    AgentReporter\Report\Diff::fromString($faker->text()),
                    AgentReporter\Report\Expected::fromString($faker->text()),
                ),
            );
        }, \range(0, $faker->numberBetween(1, 9)));

        $failedTestList = AgentReporter\Report\FailedTestList::create(...$values);

        self::assertSame($values, $failedTestList->toArray());
        self::assertEquals(AgentReporter\Report\Count::fromInt(\count($values)), $failedTestList->count());
    }
}
