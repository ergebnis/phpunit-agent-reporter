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

#[Framework\Attributes\CoversClass(Reporter\ProgressReporter::class)]
#[Framework\Attributes\UsesClass(Output\Printer::class)]
#[Framework\Attributes\UsesClass(Output\CompactRenderer::class)]
#[Framework\Attributes\UsesClass(Reporter\EventRenderer::class)]
#[Framework\Attributes\UsesClass(Output\Sanitizer::class)]
final class ProgressReporterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testTestErroredPrintsError(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $throwable = self::throwable(\RuntimeException::class);

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testErrored(
            $test,
            $throwable,
        );

        $expected = \PHP_EOL . '--- ERROR: ' . $test->nameWithClass() . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
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

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testFailed(
            $test,
            $throwable,
        );

        $expected = \PHP_EOL . '--- FAILURE: ' . $test->nameWithClass() . \PHP_EOL
            . $message . \PHP_EOL
            . \PHP_EOL
            . $stackTrace . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
    }

    public function testHookMethodErroredPrintsErrorWhenThrowableIsNotAssertionFailure(): void
    {
        $testClassName = self::class;
        $throwable = self::throwable(\RuntimeException::class);

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->hookMethodErrored(
            $testClassName,
            $throwable,
        );

        $expected = \PHP_EOL . '--- ERROR: ' . $testClassName . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
    }

    public function testHookMethodErroredPrintsFailureWhenThrowableIsAssertionFailure(): void
    {
        $testClassName = self::class;
        $throwable = self::throwable(Framework\ExpectationFailedException::class);

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->hookMethodErrored(
            $testClassName,
            $throwable,
        );

        $expected = \PHP_EOL . '--- FAILURE: ' . $testClassName . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
    }

    public function testHookMethodFailedPrintsFailure(): void
    {
        $testClassName = self::class;
        $throwable = self::throwable(Framework\ExpectationFailedException::class);

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->hookMethodFailed(
            $testClassName,
            $throwable,
        );

        $expected = \PHP_EOL . '--- FAILURE: ' . $testClassName . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
    }

    public function testTestPrintedUnexpectedOutputPrintsNothingWhenUnexpectedOutputIsNotDisplayed(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            false,
        );

        $progressReporter->testPreparationStarted($test);
        $progressReporter->testPrintedUnexpectedOutput(self::faker()->sentence() . "\n");
        $progressReporter->testRunnerExecutionFinished();

        self::assertOutputIsIdenticalTo('', $output);
    }

    public function testTestPrintedUnexpectedOutputStartsNextRecordOnNewLineWhenOutputDoesNotEndWithLineBreak(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $throwable = self::throwable(\RuntimeException::class);

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            false,
        );

        $progressReporter->testPreparationStarted($test);
        $progressReporter->testPrintedUnexpectedOutput(self::faker()->sentence());
        $progressReporter->testErrored(
            $test,
            $throwable,
        );

        $expected = \PHP_EOL
            . \PHP_EOL . '--- ERROR: ' . $test->nameWithClass() . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
    }

    public function testTestPrintedUnexpectedOutputPrintsHeaderWhenUnexpectedOutputIsDisplayed(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testPreparationStarted($test);
        $progressReporter->testPrintedUnexpectedOutput(self::faker()->sentence() . "\n");

        self::assertOutputIsIdenticalTo(\PHP_EOL . '--- OUTPUT: ' . $test->nameWithClass() . \PHP_EOL, $output);
    }

    public function testTestPrintedUnexpectedOutputPrintsHeaderWithoutTestNameWhenNoTestHasBeenPrepared(): void
    {
        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testPrintedUnexpectedOutput(self::faker()->sentence() . "\n");

        self::assertOutputIsIdenticalTo(\PHP_EOL . '--- OUTPUT: ' . \PHP_EOL, $output);
    }

    public function testTestPrintedUnexpectedOutputStartsNextRecordOnNewLineWhenUnexpectedOutputIsDisplayedAndOutputDoesNotEndWithLineBreak(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testPreparationStarted($test);
        $progressReporter->testPrintedUnexpectedOutput(self::faker()->sentence());
        $progressReporter->testRunnerExecutionFinished();

        self::assertOutputIsIdenticalTo(\PHP_EOL . '--- OUTPUT: ' . $test->nameWithClass() . \PHP_EOL . \PHP_EOL . \PHP_EOL, $output);
    }

    public function testTestRunnerExecutionFinishedPrintsNothingWhenNoRecordsHaveBeenPrinted(): void
    {
        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testRunnerExecutionFinished();

        self::assertOutputIsIdenticalTo('', $output);
    }

    public function testTestRunnerExecutionFinishedPrintsBlankLineWhenRecordsHaveBeenPrinted(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testPreparationStarted($test);
        $progressReporter->testPrintedUnexpectedOutput(self::faker()->sentence() . "\n");
        $progressReporter->testRunnerExecutionFinished();

        self::assertOutputIsIdenticalTo(\PHP_EOL . '--- OUTPUT: ' . $test->nameWithClass() . \PHP_EOL . \PHP_EOL, $output);
    }

    public function testTestRunnerExecutionFinishedPrintsBlankLineWhenErrorHasBeenPrinted(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $throwable = self::throwable(\RuntimeException::class);

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testErrored(
            $test,
            $throwable,
        );
        $progressReporter->testRunnerExecutionFinished();

        $expected = \PHP_EOL . '--- ERROR: ' . $test->nameWithClass() . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL
            . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
    }

    public function testTestRunnerExecutionFinishedPrintsBlankLineWhenFailureHasBeenPrinted(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $throwable = self::throwable(Framework\ExpectationFailedException::class);

        $output = self::outputStream();

        $progressReporter = self::progressReporter(
            $output,
            true,
        );

        $progressReporter->testFailed(
            $test,
            $throwable,
        );
        $progressReporter->testRunnerExecutionFinished();

        $expected = \PHP_EOL . '--- FAILURE: ' . $test->nameWithClass() . \PHP_EOL
            . $throwable->description() . \PHP_EOL
            . \PHP_EOL
            . $throwable->stackTrace() . \PHP_EOL
            . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
    }

    /**
     * @param resource $output
     */
    private static function progressReporter(
        $output,
        bool $displayUnexpectedOutput,
    ): Reporter\ProgressReporter {
        $compactRenderer = new Output\CompactRenderer(new Output\Sanitizer());

        return new Reporter\ProgressReporter(
            new Output\Printer($output),
            $compactRenderer,
            new Reporter\EventRenderer($compactRenderer),
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
