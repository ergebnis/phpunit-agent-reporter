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

#[Framework\Attributes\CoversClass(AgentReporter\Report\PhpunitDeprecationList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Count::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitDeprecation::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
final class PhpunitDeprecationListTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsPhpunitDeprecationListWhenValuesAreEmpty(): void
    {
        $phpunitDeprecationList = AgentReporter\Report\PhpunitDeprecationList::create();

        self::assertSame([], $phpunitDeprecationList->toArray());
        self::assertEquals(AgentReporter\Report\Count::zero(), $phpunitDeprecationList->count());
    }

    public function testCreateReturnsPhpunitDeprecationListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): AgentReporter\Report\PhpunitDeprecation {
            return AgentReporter\Report\PhpunitDeprecation::create(
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
            );
        }, \range(0, $faker->numberBetween(1, 9)));

        $phpunitDeprecationList = AgentReporter\Report\PhpunitDeprecationList::create(...$values);

        self::assertSame($values, $phpunitDeprecationList->toArray());
        self::assertEquals(AgentReporter\Report\Count::fromInt(\count($values)), $phpunitDeprecationList->count());
    }
}
