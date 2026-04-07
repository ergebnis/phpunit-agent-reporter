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

#[Framework\Attributes\CoversClass(Report\DeprecationList::class)]
#[Framework\Attributes\UsesClass(Report\Count::class)]
#[Framework\Attributes\UsesClass(Report\Deprecation::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifierList::class)]
final class DeprecationListTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsDeprecationListWhenValuesAreEmpty(): void
    {
        $deprecationList = Report\DeprecationList::create();

        self::assertSame([], $deprecationList->toArray());
        self::assertEquals(Report\Count::zero(), $deprecationList->count());
    }

    public function testCreateReturnsDeprecationListWhenValuesAreNotEmpty(): void
    {
        $faker = self::faker();

        $values = \array_map(static function () use ($faker): Report\Deprecation {
            return Report\Deprecation::create(
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

        $deprecationList = Report\DeprecationList::create(...$values);

        self::assertSame($values, $deprecationList->toArray());
        self::assertEquals(Report\Count::fromInt(\count($values)), $deprecationList->count());
    }
}
