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

namespace Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit10\WithAiAgent\Issues;

use PHPUnit\Framework;

final class ExampleTest extends Framework\TestCase
{
    public function testTriggeringDeprecation(): void
    {
        /**
         * The level is passed in a variable because friendsofphp/php-cs-fixer would otherwise suppress the deprecation.
         *
         * @see https://cs.symfony.com/doc/rules/language_construct/error_suppression.html
         */
        $level = \E_USER_DEPRECATED;

        \trigger_error(
            'Something is deprecated.',
            $level,
        );

        self::assertTrue(true);
    }

    public function testTriggeringNotice(): void
    {
        \trigger_error(
            'Something is noteworthy.',
            \E_USER_NOTICE,
        );

        self::assertTrue(true);
    }

    public function testTriggeringWarning(): void
    {
        \trigger_error(
            'Something is dangerous.',
            \E_USER_WARNING,
        );

        self::assertTrue(true);
    }
}
