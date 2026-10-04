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

namespace Ergebnis\PHPUnit\AgentReporter\Test\Unit\Output;

use Ergebnis\PHPUnit\AgentReporter\Output;
use Ergebnis\PHPUnit\AgentReporter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Output\CompactRenderer::class)]
#[Framework\Attributes\UsesClass(Output\Sanitizer::class)]
final class CompactRendererTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testHeaderReturnsHeaderLine(): void
    {
        $faker = self::faker();

        $type = \mb_strtoupper($faker->word());
        $title = $faker->sentence();

        $renderer = new Output\CompactRenderer(new Output\Sanitizer());

        $header = $renderer->header(
            $type,
            $title,
        );

        self::assertSame(\PHP_EOL . '--- ' . $type . ': ' . $title . \PHP_EOL, $header);
    }

    public function testHeaderEscapesLineBreaksAndControlCharactersInTitle(): void
    {
        $type = \mb_strtoupper(self::faker()->word());

        $renderer = new Output\CompactRenderer(new Output\Sanitizer());

        $header = $renderer->header(
            $type,
            "foo\nbar\r\nbaz\rqux\e",
        );

        self::assertSame(\PHP_EOL . '--- ' . $type . ': foo\u{000A}bar\u{000D}\u{000A}baz\u{000D}qux\u{001B}' . \PHP_EOL, $header);
    }

    public function testHeaderWithoutTitleReturnsHeaderLine(): void
    {
        $type = \mb_strtoupper(self::faker()->word());

        $renderer = new Output\CompactRenderer(new Output\Sanitizer());

        self::assertSame(\PHP_EOL . '--- ' . $type . \PHP_EOL, $renderer->headerWithoutTitle($type));
    }

    public function testBodyReturnsSanitizedBodyFollowedByLineBreak(): void
    {
        $renderer = new Output\CompactRenderer(new Output\Sanitizer());

        self::assertSame("foo\n" . 'bar\u{001B}' . \PHP_EOL, $renderer->body("foo\nbar\e"));
    }

    public function testStackTraceReturnsEmptyStringWhenStackTraceIsBlank(): void
    {
        $renderer = new Output\CompactRenderer(new Output\Sanitizer());

        self::assertSame('', $renderer->stackTrace(" \n "));
    }

    public function testStackTraceReturnsTrimmedStackTracePrecededByBlankLineWhenStackTraceIsNotBlank(): void
    {
        $faker = self::faker();

        $stackTrace = '/' . $faker->word() . '.php:' . $faker->numberBetween(1, 100);

        $renderer = new Output\CompactRenderer(new Output\Sanitizer());

        self::assertSame(\PHP_EOL . $stackTrace . \PHP_EOL, $renderer->stackTrace(\PHP_EOL . $stackTrace . \PHP_EOL . \PHP_EOL));
    }
}
