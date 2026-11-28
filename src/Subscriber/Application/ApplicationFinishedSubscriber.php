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

use Ergebnis\PHPUnit\AgentReporter;
use PHPUnit\Event;
use PHPUnit\TestRunner;

/**
 * @internal
 */
final class ApplicationFinishedSubscriber implements Event\Application\FinishedSubscriber
{
    /**
     * @param resource $output
     */
    public function __construct(
        private readonly AgentReporter\Report\TestResultTranslator $testResultTranslator,
        private readonly AgentReporter\Reporter\Reporter $reporter,
        private $output,
    ) {
    }

    public function notify(Event\Application\Finished $event): void
    {
        $testResult = TestRunner\TestResult\Facade::result();

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::fromInt($event->shellExitCode()),
            $this->testResultTranslator->erroredTestListFrom($testResult),
            $this->testResultTranslator->failedTestListFrom($testResult),
            $this->testResultTranslator->incompleteTestListFrom($testResult),
            $this->testResultTranslator->skippedTestListFrom($testResult),
            $this->testResultTranslator->riskyTestListFrom($testResult),
            $this->testResultTranslator->deprecationListFrom($testResult),
            $this->testResultTranslator->noticeListFrom($testResult),
            $this->testResultTranslator->warningListFrom($testResult),
            $this->testResultTranslator->phpunitDeprecationListFrom($testResult),
            $this->testResultTranslator->phpunitNoticeListFrom($testResult),
            $this->testResultTranslator->phpunitWarningListFrom($testResult),
            AgentReporter\Report\Count::fromInt($testResult->numberOfAssertions()),
            AgentReporter\Report\Count::fromInt($testResult->numberOfTestsRun()),
        );

        $json = $this->reporter->report($report);

        \fwrite(
            $this->output,
            $json . "\n",
        );
    }
}
