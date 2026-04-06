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

namespace Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit12\WithoutAiAgent;

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

    public function testErroring(): void
    {
        throw new \RuntimeException('Something went wrong.');
    }

    public function testSkipped(): void
    {
        self::markTestSkipped('Skipped for demonstration purposes.');
    }

    public function testIncomplete(): void
    {
        self::markTestIncomplete('Not yet implemented.');
    }

    public function testFailingStringComparison(): void
    {
        $expected = 'foo';
        $actual = 'bar';

        self::assertSame($expected, $actual);
    }

    public function testFailingIntegerComparison(): void
    {
        $expected = 1;
        $actual = 2;

        self::assertSame($expected, $actual);
    }

    public function testFailingArrayComparison(): void
    {
        $expected = [
            'bar' => 'baz',
            'foo' => 'bar',
        ];

        $actual = [
            'bar' => 'qux',
            'foo' => 'bar',
            'qux' => 'quux',
        ];

        self::assertEquals($expected, $actual);
    }
}
