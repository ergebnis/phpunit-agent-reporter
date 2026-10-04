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

namespace Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit11\WithAiAgent\TearDownAfterClassFailure;

use PHPUnit\Framework;

final class ExampleTest extends Framework\TestCase
{
    public static function tearDownAfterClass(): void
    {
        self::assertTrue(false);
    }

    public function testSucceeding(): void
    {
        self::assertTrue(true);
    }
}
