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

#[Framework\Attributes\CoversClass(AgentReporter\Report\Actual::class)]
final class ActualTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testFromStringReturnsActual(): void
    {
        $value = self::faker()->word();

        $actual = AgentReporter\Report\Actual::fromString($value);

        self::assertSame($value, $actual->toString());
    }
}
