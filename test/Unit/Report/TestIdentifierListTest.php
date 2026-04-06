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

#[Framework\Attributes\CoversClass(Report\TestIdentifierList::class)]
#[Framework\Attributes\UsesClass(Report\Count::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
final class TestIdentifierListTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsTestIdentifierListWhenValuesAreEmpty(): void
    {
        $testIdentifierList = Report\TestIdentifierList::create();

        self::assertSame([], $testIdentifierList->toArray());
        self::assertEquals(Report\Count::zero(), $testIdentifierList->count());
    }

    public function testCreateReturnsTestIdentifierListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): Report\TestIdentifier {
            return Report\TestIdentifier::fromString(\sprintf(
                '%s::%s',
                $faker->word(),
                $faker->word(),
            ));
        }, \range(0, $faker->numberBetween(1, 9)));

        $testIdentifierList = Report\TestIdentifierList::create(...$values);

        self::assertSame($values, $testIdentifierList->toArray());
        self::assertEquals(Report\Count::fromInt(\count($values)), $testIdentifierList->count());
    }
}
