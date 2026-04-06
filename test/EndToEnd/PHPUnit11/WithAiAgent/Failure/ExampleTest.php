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

namespace Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit11\WithAiAgent\Failure;

use PHPUnit\Framework;

final class ExampleTest extends Framework\TestCase
{
    public function testSucceeding(): void
    {
        self::assertTrue(true);
    }

    public function testFailing(): void
    {
        self::assertTrue(false);
    }
}
