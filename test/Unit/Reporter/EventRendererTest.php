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

namespace Ergebnis\PHPUnit\AgentReporter\Test\Unit\Reporter;

use Ergebnis\PHPUnit\AgentReporter\Output;
use Ergebnis\PHPUnit\AgentReporter\Reporter;
use Ergebnis\PHPUnit\AgentReporter\Test;
use PHPUnit\Event;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Reporter\EventRenderer::class)]
#[Framework\Attributes\UsesClass(Output\CompactRenderer::class)]
#[Framework\Attributes\UsesClass(Output\Sanitizer::class)]
final class EventRendererTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNameOfTestReturnsNameWhenTestIsNotTestMethod(): void
    {
        $test = new Event\Code\Phpt('/' . self::faker()->word() . '.phpt');

        $eventRenderer = new Reporter\EventRenderer(new Output\CompactRenderer(new Output\Sanitizer()));

        self::assertSame($test->name(), $eventRenderer->nameOfTest($test));
    }

    public function testNameOfTestReturnsNameWithClassWhenTestIsTestMethodWithoutDataFromDataProvider(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $eventRenderer = new Reporter\EventRenderer(new Output\CompactRenderer(new Output\Sanitizer()));

        self::assertSame($test->nameWithClass(), $eventRenderer->nameOfTest($test));
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

        $eventRenderer = new Reporter\EventRenderer(new Output\CompactRenderer(new Output\Sanitizer()));

        self::assertSame($test->className() . '::' . $test->methodName() . $dataAsStringForResultOutput, $eventRenderer->nameOfTest($test));
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

        $eventRenderer = new Reporter\EventRenderer(new Output\CompactRenderer(new Output\Sanitizer()));

        $expected = $description . \PHP_EOL
            . \PHP_EOL
            . $stackTrace . \PHP_EOL;

        self::assertSame($expected, $eventRenderer->throwable($throwable));
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

        $eventRenderer = new Reporter\EventRenderer(new Output\CompactRenderer(new Output\Sanitizer()));

        $expected = $description . \PHP_EOL
            . \PHP_EOL
            . $stackTrace . \PHP_EOL
            . 'Caused by' . \PHP_EOL
            . $previousDescription . \PHP_EOL
            . \PHP_EOL
            . $previousStackTrace . \PHP_EOL;

        self::assertSame($expected, $eventRenderer->throwable($throwable));
    }
}
