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

namespace Ergebnis\PHPUnit\AgentReporter\Subscriber\Application;

use Ergebnis\PHPUnit\AgentReporter\Report;
use Ergebnis\PHPUnit\AgentReporter\Reporter;
use PHPUnit\Event;
use PHPUnit\TestRunner;

/**
 * @internal
 */
final class ApplicationFinishedSubscriber implements Event\Application\FinishedSubscriber
{
    private readonly Report\TestResultTranslator $testResultTranslator;
    private readonly Reporter\Reporter $reporter;

    /**
     * @var resource
     */
    private $output;

    /**
     * @param resource $output
     */
    public function __construct(
        Report\TestResultTranslator $testResultTranslator,
        Reporter\Reporter $reporter,
        $output,
    ) {
        $this->testResultTranslator = $testResultTranslator;
        $this->reporter = $reporter;
        $this->output = $output;
    }

    public function notify(Event\Application\Finished $event): void
    {
        $testResult = TestRunner\TestResult\Facade::result();

        $report = Report\Report::create(
            Report\ShellExitCode::fromInt($event->shellExitCode()),
            $this->testResultTranslator->erroredTestListFrom($testResult),
            $this->testResultTranslator->failedTestListFrom($testResult),
            $this->testResultTranslator->incompleteTestListFrom($testResult),
            $this->testResultTranslator->skippedTestListFrom($testResult),
            $this->testResultTranslator->riskyTestListFrom($testResult),
            $this->testResultTranslator->deprecationListFrom($testResult),
            $this->testResultTranslator->noticeListFrom($testResult),
            $this->testResultTranslator->warningListFrom($testResult),
            Report\Count::fromInt($testResult->numberOfTestsRun()),
        );

        $json = $this->reporter->report($report);

        \fwrite(
            $this->output,
            $json . "\n",
        );
    }
}
