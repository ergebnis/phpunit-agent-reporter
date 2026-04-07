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

#[Framework\Attributes\CoversClass(Report\Message::class)]
final class MessageTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromStringReturnsMessage(): void
    {
        $value = self::faker()->sentence();

        $message = Report\Message::fromString($value);

        self::assertSame($value, $message->toString());
    }
}
