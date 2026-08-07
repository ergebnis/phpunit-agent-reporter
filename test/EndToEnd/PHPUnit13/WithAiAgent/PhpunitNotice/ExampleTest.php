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

namespace Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit13\WithAiAgent\PhpunitNotice;

use PHPUnit\Framework;

final class ExampleTest extends Framework\TestCase
{
    public function testTriggeringPhpunitNotice(): void
    {
        $countable = $this->createMock(\Countable::class);

        self::assertInstanceOf(\Countable::class, $countable);
    }
}
