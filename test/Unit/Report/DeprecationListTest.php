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

#[Framework\Attributes\CoversClass(AgentReporter\Report\DeprecationList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Count::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Deprecation::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifierList::class)]
final class DeprecationListTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testCreateReturnsDeprecationListWhenValuesAreEmpty(): void
    {
        $deprecationList = AgentReporter\Report\DeprecationList::create();

        self::assertSame([], $deprecationList->toArray());
        self::assertEquals(AgentReporter\Report\Count::zero(), $deprecationList->count());
    }

    public function testCreateReturnsDeprecationListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): AgentReporter\Report\Deprecation {
            return AgentReporter\Report\Deprecation::create(
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

        $deprecationList = AgentReporter\Report\DeprecationList::create(...$values);

        self::assertSame($values, $deprecationList->toArray());
        self::assertEquals(AgentReporter\Report\Count::fromInt(\count($values)), $deprecationList->count());
    }
}
