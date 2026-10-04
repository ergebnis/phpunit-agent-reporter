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

namespace Ergebnis\PHPUnit\AgentReporter\Output;

use PHPUnit\Event;
use PHPUnit\Framework;

/**
 * Prints errors, failures, and the headers of output printed by tests as they happen.
 *
 * @internal
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Compact/ProgressPrinter/ProgressPrinter.php
 */
final class ProgressPrinter
{
    private readonly Printer $printer;
    private readonly Renderer $renderer;
    private readonly bool $displayUnexpectedOutput;
    private ?Event\Code\Test $currentTest = null;
    private bool $hasPrintedRecords = false;

    public function __construct(
        Printer $printer,
        Renderer $renderer,
        bool $displayUnexpectedOutput,
    ) {
        $this->printer = $printer;
        $this->renderer = $renderer;
        $this->displayUnexpectedOutput = $displayUnexpectedOutput;
    }

    public function testPreparationStarted(Event\Code\Test $test): void
    {
        $this->currentTest = $test;
    }

    public function testErrored(
        Event\Code\Test $test,
        Event\Code\Throwable $throwable,
    ): void {
        $this->printError(
            $this->renderer->nameOfTest($test),
            $throwable,
        );
    }

    public function testFailed(
        Event\Code\Test $test,
        Event\Code\Throwable $throwable,
    ): void {
        $this->printFailure(
            $this->renderer->nameOfTest($test),
            $throwable,
        );
    }

    /**
     * Prints only the header of the record, because phpunit/phpunit prints the output itself, right after, whenever its compact output is not enabled.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Facade.php
     * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Default/UnexpectedOutputPrinter.php
     */
    public function testPrintedUnexpectedOutput(string $output): void
    {
        if (!$this->displayUnexpectedOutput) {
            $this->printer->printedElsewhere($output);

            return;
        }

        $title = '';

        if ($this->currentTest instanceof Event\Code\Test) {
            $title = $this->renderer->nameOfTest($this->currentTest);
        }

        $this->printer->print($this->renderer->header(
            'OUTPUT',
            $title,
        ));

        $this->printer->printedElsewhere($output);

        $this->hasPrintedRecords = true;
    }

    /**
     * Prints a failure instead of an error when the throwable is an assertion failure, because phpunit/phpunit emits dedicated events for assertion failures in hook methods only since 12.2.0.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/12.2.0/src/Event/Events/Test/HookMethod/BeforeFirstTestMethodFailed.php
     * @see https://github.com/sebastianbergmann/phpunit/blob/12.2.0/src/Event/Events/Test/HookMethod/AfterLastTestMethodFailed.php
     */
    public function hookMethodErrored(
        string $testClassName,
        Event\Code\Throwable $throwable,
    ): void {
        if (\is_a($throwable->className(), Framework\AssertionFailedError::class, true)) {
            $this->printFailure(
                $testClassName,
                $throwable,
            );

            return;
        }

        $this->printError(
            $testClassName,
            $throwable,
        );
    }

    public function hookMethodFailed(
        string $testClassName,
        Event\Code\Throwable $throwable,
    ): void {
        $this->printFailure(
            $testClassName,
            $throwable,
        );
    }

    /**
     * Separates the records printed during the run from the summary that follows.
     */
    public function testRunnerExecutionFinished(): void
    {
        if ($this->hasPrintedRecords) {
            $this->printer->print(\PHP_EOL);
        }
    }

    private function printError(
        string $title,
        Event\Code\Throwable $throwable,
    ): void {
        $this->printer->print($this->renderer->header(
            'ERROR',
            $title,
        ) . $this->renderer->throwable($throwable));

        $this->hasPrintedRecords = true;
    }

    private function printFailure(
        string $title,
        Event\Code\Throwable $throwable,
    ): void {
        $body = $throwable->description();

        if (\str_starts_with($body, 'AssertionError: ')) {
            $body = \mb_substr(
                $body,
                \mb_strlen('AssertionError: '),
            );
        }

        $this->printer->print($this->renderer->header(
            'FAILURE',
            $title,
        ) . $this->renderer->body(\trim($body)) . $this->renderer->stackTrace($throwable->stackTrace()));

        $this->hasPrintedRecords = true;
    }
}
