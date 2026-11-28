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

#[Framework\Attributes\CoversClass(AgentReporter\Report\TestIdentifierList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Count::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
final class TestIdentifierListTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsTestIdentifierListWhenValuesAreEmpty(): void
    {
        $testIdentifierList = AgentReporter\Report\TestIdentifierList::create();

        self::assertSame([], $testIdentifierList->toArray());
        self::assertEquals(AgentReporter\Report\Count::zero(), $testIdentifierList->count());
    }

    public function testCreateReturnsTestIdentifierListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
            return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                '%s::%s',
                $faker->word(),
                $faker->word(),
            ));
        }, \range(0, $faker->numberBetween(1, 9)));

        $testIdentifierList = AgentReporter\Report\TestIdentifierList::create(...$values);

        self::assertSame($values, $testIdentifierList->toArray());
        self::assertEquals(AgentReporter\Report\Count::fromInt(\count($values)), $testIdentifierList->count());
    }
}
