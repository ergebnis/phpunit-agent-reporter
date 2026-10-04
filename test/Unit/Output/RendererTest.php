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
use PHPUnit\Event;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Output\Renderer::class)]
#[Framework\Attributes\UsesClass(Output\Sanitizer::class)]
final class RendererTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNameOfTestReturnsNameWhenTestIsNotTestMethod(): void
    {
        $test = new Event\Code\Phpt('/' . self::faker()->word() . '.phpt');

        $renderer = new Output\Renderer(new Output\Sanitizer());

        self::assertSame($test->name(), $renderer->nameOfTest($test));
    }

    public function testNameOfTestReturnsNameWithClassWhenTestIsTestMethodWithoutDataFromDataProvider(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $renderer = new Output\Renderer(new Output\Sanitizer());

        self::assertSame($test->nameWithClass(), $renderer->nameOfTest($test));
    }

    public function testNameOfTestReturnsClassNameMethodNameAndDataForResultOutputWhenTestIsTestMethodWithDataFromDataProvider(): void
    {
        $faker = self::faker();

        $dataAsStringForResultOutput = ' with data set "' . $faker->word() . '"';

        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([
            Event\TestData\DataFromDataProvider::from(
                $faker->word(),
                $faker->sentence(),
                $dataAsStringForResultOutput,
            ),
        ]));

        $renderer = new Output\Renderer(new Output\Sanitizer());

        self::assertSame($test->className() . '::' . $test->methodName() . $dataAsStringForResultOutput, $renderer->nameOfTest($test));
    }

    public function testHeaderReturnsHeaderLine(): void
    {
        $faker = self::faker();

        $type = \mb_strtoupper($faker->word());
        $title = $faker->sentence();

        $renderer = new Output\Renderer(new Output\Sanitizer());

        $header = $renderer->header(
            $type,
            $title,
        );

        self::assertSame(\PHP_EOL . '--- ' . $type . ': ' . $title . \PHP_EOL, $header);
    }

    public function testHeaderEscapesLineBreaksAndControlCharactersInTitle(): void
    {
        $type = \mb_strtoupper(self::faker()->word());

        $renderer = new Output\Renderer(new Output\Sanitizer());

        $header = $renderer->header(
            $type,
            "foo\nbar\r\nbaz\rqux\e",
        );

        self::assertSame(\PHP_EOL . '--- ' . $type . ': foo\u{000A}bar\u{000D}\u{000A}baz\u{000D}qux\u{001B}' . \PHP_EOL, $header);
    }

    public function testHeaderWithoutTitleReturnsHeaderLine(): void
    {
        $type = \mb_strtoupper(self::faker()->word());

        $renderer = new Output\Renderer(new Output\Sanitizer());

        self::assertSame(\PHP_EOL . '--- ' . $type . \PHP_EOL, $renderer->headerWithoutTitle($type));
    }

    public function testBodyReturnsSanitizedBodyFollowedByLineBreak(): void
    {
        $renderer = new Output\Renderer(new Output\Sanitizer());

        self::assertSame("foo\n" . 'bar\u{001B}' . \PHP_EOL, $renderer->body("foo\nbar\e"));
    }

    public function testStackTraceReturnsEmptyStringWhenStackTraceIsBlank(): void
    {
        $renderer = new Output\Renderer(new Output\Sanitizer());

        self::assertSame('', $renderer->stackTrace(" \n "));
    }

    public function testStackTraceReturnsTrimmedStackTracePrecededByBlankLineWhenStackTraceIsNotBlank(): void
    {
        $faker = self::faker();

        $stackTrace = '/' . $faker->word() . '.php:' . $faker->numberBetween(1, 100);

        $renderer = new Output\Renderer(new Output\Sanitizer());

        self::assertSame(\PHP_EOL . $stackTrace . \PHP_EOL, $renderer->stackTrace(\PHP_EOL . $stackTrace . \PHP_EOL . \PHP_EOL));
    }

    public function testThrowableReturnsDescriptionAndStackTraceWhenThrowableDoesNotHavePrevious(): void
    {
        $faker = self::faker();

        $description = $faker->sentence();
        $stackTrace = '/' . $faker->word() . '.php:' . $faker->numberBetween(1, 100);

        $throwable = new Event\Code\Throwable(
            \RuntimeException::class,
            $faker->sentence(),
            $description . \PHP_EOL,
            $stackTrace . \PHP_EOL,
            null,
        );

        $renderer = new Output\Renderer(new Output\Sanitizer());

        $expected = $description . \PHP_EOL
            . \PHP_EOL
            . $stackTrace . \PHP_EOL;

        self::assertSame($expected, $renderer->throwable($throwable));
    }

    public function testThrowableReturnsDescriptionStackTraceAndPreviousWhenThrowableHasPrevious(): void
    {
        $faker = self::faker();

        $previousDescription = $faker->sentence();
        $previousStackTrace = '/' . $faker->word() . '.php:' . $faker->numberBetween(1, 100);
        $description = $faker->sentence();
        $stackTrace = '/' . $faker->word() . '.php:' . $faker->numberBetween(1, 100);

        $throwable = new Event\Code\Throwable(
            \RuntimeException::class,
            $faker->sentence(),
            $description,
            $stackTrace,
            new Event\Code\Throwable(
                \LogicException::class,
                $faker->sentence(),
                $previousDescription,
                $previousStackTrace,
                null,
            ),
        );

        $renderer = new Output\Renderer(new Output\Sanitizer());

        $expected = $description . \PHP_EOL
            . \PHP_EOL
            . $stackTrace . \PHP_EOL
            . 'Caused by' . \PHP_EOL
            . $previousDescription . \PHP_EOL
            . \PHP_EOL
            . $previousStackTrace . \PHP_EOL;

        self::assertSame($expected, $renderer->throwable($throwable));
    }
}
