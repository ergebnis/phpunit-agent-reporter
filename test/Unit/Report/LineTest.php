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

#[Framework\Attributes\CoversClass(Report\Line::class)]
final class LineTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromIntReturnsLine(): void
    {
        $value = self::faker()->numberBetween(0, 500);

        $line = Report\Line::fromInt($value);

        self::assertSame($value, $line->toInt());
    }
}
