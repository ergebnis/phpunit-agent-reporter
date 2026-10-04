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

#[Framework\Attributes\CoversClass(Output\ProgressPrinter::class)]
#[Framework\Attributes\UsesClass(Output\Printer::class)]
#[Framework\Attributes\UsesClass(Output\Renderer::class)]
#[Framework\Attributes\UsesClass(Output\Sanitizer::class)]
final class ProgressPrinterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testTestErroredPrintsError(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $throwable = self::throwable(\RuntimeException::class);

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testErrored(
            $test,
            $throwable,
        );

        $expected = \PHP_EOL . '--- ERROR: ' . $test->nameWithClass() . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertSame($expected, self::contentsOf($output));
    }

    public function testTestFailedPrintsFailureWithoutAssertionErrorPrefix(): void
    {
        $faker = self::faker();

        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $message = $faker->sentence();
        $stackTrace = '/' . $faker->word() . '.php:' . $faker->numberBetween(1, 100);

        $throwable = new Event\Code\Throwable(
            \AssertionError::class,
            $message,
            'AssertionError: ' . $message . \PHP_EOL,
            $stackTrace,
            null,
        );

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testFailed(
            $test,
            $throwable,
        );

        $expected = \PHP_EOL . '--- FAILURE: ' . $test->nameWithClass() . \PHP_EOL
            . $message . \PHP_EOL
            . \PHP_EOL
            . $stackTrace . \PHP_EOL;

        self::assertSame($expected, self::contentsOf($output));
    }

    public function testHookMethodErroredPrintsErrorWhenThrowableIsNotAssertionFailure(): void
    {
        $testClassName = self::class;
        $throwable = self::throwable(\RuntimeException::class);

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->hookMethodErrored(
            $testClassName,
            $throwable,
        );

        $expected = \PHP_EOL . '--- ERROR: ' . $testClassName . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertSame($expected, self::contentsOf($output));
    }

    public function testHookMethodErroredPrintsFailureWhenThrowableIsAssertionFailure(): void
    {
        $testClassName = self::class;
        $throwable = self::throwable(Framework\ExpectationFailedException::class);

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->hookMethodErrored(
            $testClassName,
            $throwable,
        );

        $expected = \PHP_EOL . '--- FAILURE: ' . $testClassName . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertSame($expected, self::contentsOf($output));
    }

    public function testHookMethodFailedPrintsFailure(): void
    {
        $testClassName = self::class;
        $throwable = self::throwable(Framework\ExpectationFailedException::class);

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->hookMethodFailed(
            $testClassName,
            $throwable,
        );

        $expected = \PHP_EOL . '--- FAILURE: ' . $testClassName . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertSame($expected, self::contentsOf($output));
    }

    public function testTestPrintedUnexpectedOutputPrintsNothingWhenUnexpectedOutputIsNotDisplayed(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            false,
        );

        $progressPrinter->testPreparationStarted($test);
        $progressPrinter->testPrintedUnexpectedOutput(self::faker()->sentence() . "\n");
        $progressPrinter->testRunnerExecutionFinished();

        self::assertSame('', self::contentsOf($output));
    }

    public function testTestPrintedUnexpectedOutputStartsNextRecordOnNewLineWhenOutputDoesNotEndWithLineBreak(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $throwable = self::throwable(\RuntimeException::class);

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            false,
        );

        $progressPrinter->testPreparationStarted($test);
        $progressPrinter->testPrintedUnexpectedOutput(self::faker()->sentence());
        $progressPrinter->testErrored(
            $test,
            $throwable,
        );

        $expected = \PHP_EOL
            . \PHP_EOL . '--- ERROR: ' . $test->nameWithClass() . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertSame($expected, self::contentsOf($output));
    }

    public function testTestPrintedUnexpectedOutputPrintsHeaderWhenUnexpectedOutputIsDisplayed(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testPreparationStarted($test);
        $progressPrinter->testPrintedUnexpectedOutput(self::faker()->sentence() . "\n");

        self::assertSame(\PHP_EOL . '--- OUTPUT: ' . $test->nameWithClass() . \PHP_EOL, self::contentsOf($output));
    }

    public function testTestPrintedUnexpectedOutputPrintsHeaderWithoutTestNameWhenNoTestHasBeenPrepared(): void
    {
        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testPrintedUnexpectedOutput(self::faker()->sentence() . "\n");

        self::assertSame(\PHP_EOL . '--- OUTPUT: ' . \PHP_EOL, self::contentsOf($output));
    }

    public function testTestPrintedUnexpectedOutputStartsNextRecordOnNewLineWhenUnexpectedOutputIsDisplayedAndOutputDoesNotEndWithLineBreak(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testPreparationStarted($test);
        $progressPrinter->testPrintedUnexpectedOutput(self::faker()->sentence());
        $progressPrinter->testRunnerExecutionFinished();

        self::assertSame(\PHP_EOL . '--- OUTPUT: ' . $test->nameWithClass() . \PHP_EOL . \PHP_EOL . \PHP_EOL, self::contentsOf($output));
    }

    public function testTestRunnerExecutionFinishedPrintsNothingWhenNoRecordsHaveBeenPrinted(): void
    {
        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testRunnerExecutionFinished();

        self::assertSame('', self::contentsOf($output));
    }

    public function testTestRunnerExecutionFinishedPrintsBlankLineWhenRecordsHaveBeenPrinted(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testPreparationStarted($test);
        $progressPrinter->testPrintedUnexpectedOutput(self::faker()->sentence() . "\n");
        $progressPrinter->testRunnerExecutionFinished();

        self::assertSame(\PHP_EOL . '--- OUTPUT: ' . $test->nameWithClass() . \PHP_EOL . \PHP_EOL, self::contentsOf($output));
    }

    public function testTestRunnerExecutionFinishedPrintsBlankLineWhenErrorHasBeenPrinted(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $throwable = self::throwable(\RuntimeException::class);

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testErrored(
            $test,
            $throwable,
        );
        $progressPrinter->testRunnerExecutionFinished();

        self::assertStringEndsWith(\PHP_EOL . \PHP_EOL, self::contentsOf($output));
    }

    public function testTestRunnerExecutionFinishedPrintsBlankLineWhenFailureHasBeenPrinted(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $throwable = self::throwable(Framework\ExpectationFailedException::class);

        $output = self::memoryStream();

        $progressPrinter = self::progressPrinter(
            $output,
            true,
        );

        $progressPrinter->testFailed(
            $test,
            $throwable,
        );
        $progressPrinter->testRunnerExecutionFinished();

        self::assertStringEndsWith(\PHP_EOL . \PHP_EOL, self::contentsOf($output));
    }

    /**
     * @param resource $output
     */
    private static function progressPrinter(
        $output,
        bool $displayUnexpectedOutput,
    ): Output\ProgressPrinter {
        return new Output\ProgressPrinter(
            new Output\Printer($output),
            new Output\Renderer(new Output\Sanitizer()),
            $displayUnexpectedOutput,
        );
    }

    /**
     * @param class-string<\Throwable> $className
     */
    private static function throwable(string $className): Event\Code\Throwable
    {
        $faker = self::faker();

        $message = $faker->sentence();

        return new Event\Code\Throwable(
            $className,
            $message,
            $className . ': ' . $message,
            '/' . $faker->word() . '.php:' . $faker->numberBetween(1, 100),
            null,
        );
    }
}
