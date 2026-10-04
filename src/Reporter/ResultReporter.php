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

namespace Ergebnis\PHPUnit\AgentReporter\Reporter;

use Ergebnis\PHPUnit\AgentReporter\Output;
use PHPUnit\Event;
use PHPUnit\TestRunner;

/**
 * Prints the summary line and the records that follow it.
 *
 * phpunit/phpunit prints the message that the time limit for the test run was exceeded itself whenever its compact output is not enabled, so this printer does not print a record for it.
 *
 * @internal
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Compact/ResultPrinter.php
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Application.php
 */
final class ResultReporter
{
    private readonly Output\Printer $printer;
    private readonly Output\CompactRenderer $compactRenderer;
    private readonly EventRenderer $eventRenderer;
    private readonly bool $displayDetailsOnIncompleteTests;
    private readonly bool $displayDetailsOnSkippedTests;
    private readonly bool $displayDetailsOnTestsThatTriggerDeprecations;
    private readonly bool $displayDetailsOnTestsThatTriggerErrors;
    private readonly bool $displayDetailsOnTestsThatTriggerNotices;
    private readonly bool $displayDetailsOnTestsThatTriggerWarnings;
    private readonly bool $displayDetailsOnPhpunitDeprecations;
    private readonly bool $displayDetailsOnPhpunitNotices;

    public function __construct(
        Output\Printer $printer,
        Output\CompactRenderer $compactRenderer,
        EventRenderer $eventRenderer,
        bool $displayDetailsOnIncompleteTests,
        bool $displayDetailsOnSkippedTests,
        bool $displayDetailsOnTestsThatTriggerDeprecations,
        bool $displayDetailsOnTestsThatTriggerErrors,
        bool $displayDetailsOnTestsThatTriggerNotices,
        bool $displayDetailsOnTestsThatTriggerWarnings,
        bool $displayDetailsOnPhpunitDeprecations,
        bool $displayDetailsOnPhpunitNotices,
    ) {
        $this->printer = $printer;
        $this->compactRenderer = $compactRenderer;
        $this->eventRenderer = $eventRenderer;
        $this->displayDetailsOnIncompleteTests = $displayDetailsOnIncompleteTests;
        $this->displayDetailsOnSkippedTests = $displayDetailsOnSkippedTests;
        $this->displayDetailsOnTestsThatTriggerDeprecations = $displayDetailsOnTestsThatTriggerDeprecations;
        $this->displayDetailsOnTestsThatTriggerErrors = $displayDetailsOnTestsThatTriggerErrors;
        $this->displayDetailsOnTestsThatTriggerNotices = $displayDetailsOnTestsThatTriggerNotices;
        $this->displayDetailsOnTestsThatTriggerWarnings = $displayDetailsOnTestsThatTriggerWarnings;
        $this->displayDetailsOnPhpunitDeprecations = $displayDetailsOnPhpunitDeprecations;
        $this->displayDetailsOnPhpunitNotices = $displayDetailsOnPhpunitNotices;
    }

    public function print(TestRunner\TestResult\TestResult $result): void
    {
        if (0 === $result->numberOfTestsRun()) {
            $this->printer->print('No tests executed!' . \PHP_EOL);

            return;
        }

        $this->printSummaryLine($result);
        $this->printPhpunitErrors($result);
        $this->printTestRunnerWarnings($result);

        if ($this->displayDetailsOnPhpunitDeprecations) {
            $this->printTestRunnerDeprecations($result);
        }

        if ($this->displayDetailsOnPhpunitNotices) {
            $this->printTestRunnerNotices($result);
        }

        $this->printPhpunitWarnings($result);

        if ($this->displayDetailsOnPhpunitDeprecations) {
            $this->printPhpunitDeprecations($result);
        }

        if ($this->displayDetailsOnPhpunitNotices) {
            $this->printPhpunitNotices($result);
        }

        if ($this->displayDetailsOnTestsThatTriggerDeprecations) {
            $this->printIssueList(
                'DEPRECATION',
                ...$result->phpDeprecations(),
                ...$result->deprecations(),
            );
            $this->printIssuesTriggeredOutsideOfTests(
                $result,
                'testRunnerTriggeredIssuePhpDeprecationEvents',
                'PHP DEPRECATION',
            );
            $this->printIssuesTriggeredOutsideOfTests(
                $result,
                'testRunnerTriggeredIssueDeprecationEvents',
                'DEPRECATION',
            );
        }

        if ($this->displayDetailsOnTestsThatTriggerWarnings) {
            $this->printIssueList(
                'WARNING',
                ...$result->phpWarnings(),
                ...$result->warnings(),
            );
            $this->printIssuesTriggeredOutsideOfTests(
                $result,
                'testRunnerTriggeredIssuePhpWarningEvents',
                'PHP WARNING',
            );
            $this->printIssuesTriggeredOutsideOfTests(
                $result,
                'testRunnerTriggeredIssueWarningEvents',
                'WARNING',
            );
        }

        if ($this->displayDetailsOnTestsThatTriggerNotices) {
            $this->printIssueList(
                'NOTICE',
                ...$result->phpNotices(),
                ...$result->notices(),
            );
            $this->printIssuesTriggeredOutsideOfTests(
                $result,
                'testRunnerTriggeredIssuePhpNoticeEvents',
                'PHP NOTICE',
            );
            $this->printIssuesTriggeredOutsideOfTests(
                $result,
                'testRunnerTriggeredIssueNoticeEvents',
                'NOTICE',
            );
        }

        if ($this->displayDetailsOnTestsThatTriggerErrors) {
            $this->printIssueList(
                'ERROR',
                ...$result->errors(),
            );
            $this->printIssuesTriggeredOutsideOfTests(
                $result,
                'testRunnerTriggeredIssueErrorEvents',
                'ERROR',
            );
        }

        $this->printRiskyTests($result);

        if ($this->displayDetailsOnIncompleteTests) {
            $this->printIncompleteTests($result);
        }

        if ($this->displayDetailsOnSkippedTests) {
            $this->printSkippedTests($result);
        }
    }

    private function printSummaryLine(TestRunner\TestResult\TestResult $result): void
    {
        $counts = [
            self::count(
                $result->numberOfTestsRun(),
                'test',
                'tests',
            ),
            self::count(
                $result->numberOfAssertions(),
                'assertion',
                'assertions',
            ),
        ];

        $optionalCounts = [
            [
                $result->numberOfErrors(),
                'error',
                'errors',
            ],
            [
                $result->numberOfTestFailedEvents(),
                'failure',
                'failures',
            ],
            [
                $result->numberOfPhpOrUserDeprecations(),
                'deprecation',
                'deprecations',
            ],
            [
                $result->numberOfPhpunitDeprecations(),
                'PHPUnit deprecation',
                'PHPUnit deprecations',
            ],
            [
                $result->numberOfWarnings(),
                'warning',
                'warnings',
            ],
            [
                $result->numberOfPhpunitWarnings(),
                'PHPUnit warning',
                'PHPUnit warnings',
            ],
            [
                $result->numberOfNotices(),
                'notice',
                'notices',
            ],
            [
                self::numberOfPhpunitNotices($result),
                'PHPUnit notice',
                'PHPUnit notices',
            ],
            [
                self::numberOfTestSkippedByTestSuiteSkippedEvents($result) + $result->numberOfTestSkippedEvents(),
                'skipped',
                'skipped',
            ],
            [
                $result->numberOfTestMarkedIncompleteEvents(),
                'incomplete',
                'incomplete',
            ],
            [
                $result->numberOfTestsWithTestConsideredRiskyEvents(),
                'risky',
                'risky',
            ],
        ];

        foreach ($optionalCounts as [$count, $singular, $plural]) {
            if (0 < $count) {
                $counts[] = self::count(
                    $count,
                    $singular,
                    $plural,
                );
            }
        }

        $status = 'FAILURES';

        if ($result->wasSuccessful()) {
            $status = 'OK';
        } elseif (
            $result->hasTestErroredEvents()
            || $result->hasTestTriggeredPhpunitErrorEvents()
        ) {
            $status = 'ERRORS';
        }

        $this->printer->print(\sprintf(
            '%s (%s)' . \PHP_EOL,
            $status,
            \implode(
                ', ',
                $counts,
            ),
        ));
    }

    private function printPhpunitErrors(TestRunner\TestResult\TestResult $result): void
    {
        foreach ($result->testTriggeredPhpunitErrorEvents() as $events) {
            $this->printTestRecord(
                'PHPUNIT ERROR',
                $events[0]->test(),
                ...\array_map(static function (Event\Test\PhpunitErrorTriggered $event): string {
                    return \trim($event->message());
                }, $events),
            );
        }
    }

    private function printPhpunitWarnings(TestRunner\TestResult\TestResult $result): void
    {
        foreach ($result->testTriggeredPhpunitWarningEvents() as $events) {
            $this->printTestRecord(
                'PHPUNIT WARNING',
                $events[0]->test(),
                ...\array_map(static function (Event\Test\PhpunitWarningTriggered $event): string {
                    return \trim($event->message());
                }, $events),
            );
        }
    }

    private function printPhpunitDeprecations(TestRunner\TestResult\TestResult $result): void
    {
        foreach ($result->testTriggeredPhpunitDeprecationEvents() as $events) {
            $this->printTestRecord(
                'PHPUNIT DEPRECATION',
                $events[0]->test(),
                ...\array_map(static function (Event\Test\PhpunitDeprecationTriggered $event): string {
                    return \trim($event->message());
                }, $events),
            );
        }
    }

    /**
     * phpunit/phpunit emits events for notices triggered by itself only since 12.1.0.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/12.1.0/src/Event/Events/Test/Issue/PhpunitNoticeTriggered.php
     */
    private function printPhpunitNotices(TestRunner\TestResult\TestResult $result): void
    {
        if (!\method_exists($result, 'testTriggeredPhpunitNoticeEvents')) {
            return;
        }

        $eventsByTest = $result->testTriggeredPhpunitNoticeEvents();

        if (!\is_array($eventsByTest)) {
            return;
        }

        foreach ($eventsByTest as $events) {
            if (!\is_array($events)) {
                continue;
            }

            $test = null;
            $messages = [];

            foreach ($events as $event) {
                if (
                    !\is_object($event)
                    || !\method_exists($event, 'test')
                    || !\method_exists($event, 'message')
                ) {
                    continue;
                }

                $eventTest = $event->test();
                $message = $event->message();

                if (
                    !$eventTest instanceof Event\Code\Test
                    || !\is_string($message)
                ) {
                    continue;
                }

                $test = $eventTest;
                $messages[] = \trim($message);
            }

            if (!$test instanceof Event\Code\Test) {
                continue;
            }

            $this->printTestRecord(
                'PHPUNIT NOTICE',
                $test,
                ...$messages,
            );
        }
    }

    private function printTestRecord(
        string $type,
        Event\Code\Test $test,
        string ...$messages,
    ): void {
        $record = $this->compactRenderer->header(
            $type,
            $this->eventRenderer->nameOfTest($test),
        );

        foreach ($messages as $message) {
            $record .= $this->compactRenderer->body($message);
        }

        $this->printer->print($record);
    }

    private function printTestRunnerWarnings(TestRunner\TestResult\TestResult $result): void
    {
        $this->printTestRunnerRecords(
            'PHPUNIT TEST RUNNER WARNING',
            true,
            ...\array_map(static function (Event\TestRunner\WarningTriggered $event): string {
                return $event->message();
            }, $result->testRunnerTriggeredWarningEvents()),
        );
    }

    private function printTestRunnerDeprecations(TestRunner\TestResult\TestResult $result): void
    {
        $this->printTestRunnerRecords(
            'PHPUNIT TEST RUNNER DEPRECATION',
            false,
            ...\array_map(static function (Event\TestRunner\DeprecationTriggered $event): string {
                return $event->message();
            }, $result->testRunnerTriggeredDeprecationEvents()),
        );
    }

    /**
     * phpunit/phpunit emits events for notices triggered by its test runner only since 12.1.0.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/12.1.0/src/Event/Events/TestRunner/NoticeTriggered.php
     */
    private function printTestRunnerNotices(TestRunner\TestResult\TestResult $result): void
    {
        if (!\method_exists($result, 'testRunnerTriggeredNoticeEvents')) {
            return;
        }

        $events = $result->testRunnerTriggeredNoticeEvents();

        if (!\is_array($events)) {
            return;
        }

        $messages = [];

        foreach ($events as $event) {
            if (
                !\is_object($event)
                || !\method_exists($event, 'message')
            ) {
                continue;
            }

            $message = $event->message();

            if (!\is_string($message)) {
                continue;
            }

            $messages[] = $message;
        }

        $this->printTestRunnerRecords(
            'PHPUNIT TEST RUNNER NOTICE',
            true,
            ...$messages,
        );
    }

    private function printTestRunnerRecords(
        string $type,
        bool $deduplicate,
        string ...$messages,
    ): void {
        $printedMessages = [];

        foreach ($messages as $message) {
            if (
                $deduplicate
                && \in_array($message, $printedMessages, true)
            ) {
                continue;
            }

            $printedMessages[] = $message;

            $this->printer->print($this->compactRenderer->headerWithoutTitle($type) . $this->compactRenderer->body(\trim($message)));
        }
    }

    /**
     * phpunit/phpunit collects issues triggered outside of tests only since 13.2.0.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/13.2.0/src/Runner/TestResult/TestResult.php
     */
    private function printIssuesTriggeredOutsideOfTests(
        TestRunner\TestResult\TestResult $result,
        string $methodName,
        string $type,
    ): void {
        if (!\method_exists($result, $methodName)) {
            return;
        }

        $events = $result->{$methodName}();

        if (!\is_array($events)) {
            return;
        }

        $seen = [];

        foreach ($events as $event) {
            if (
                !\is_object($event)
                || !\method_exists($event, 'file')
                || !\method_exists($event, 'line')
                || !\method_exists($event, 'message')
            ) {
                continue;
            }

            $file = $event->file();
            $line = $event->line();
            $message = $event->message();

            if (
                !\is_string($file)
                || !\is_int($line)
                || !\is_string($message)
            ) {
                continue;
            }

            $key = $file . ':' . $line . ':' . $message;

            if (\array_key_exists($key, $seen)) {
                continue;
            }

            $seen[$key] = true;

            $this->printer->print($this->compactRenderer->header(
                $type,
                $file . ':' . $line,
            ) . $this->compactRenderer->body(\trim($message)));
        }
    }

    private function printIssueList(
        string $type,
        TestRunner\TestResult\Issues\Issue ...$issues,
    ): void {
        foreach ($issues as $issue) {
            $record = $this->compactRenderer->header(
                $type,
                $issue->file() . ':' . $issue->line(),
            ) . $this->compactRenderer->body(\trim($issue->description()));

            if (!self::triggeredInTest($issue)) {
                $triggeringTests = $issue->triggeringTests();

                \ksort($triggeringTests);

                foreach ($triggeringTests as $triggeringTest) {
                    $test = $triggeringTest['test'];

                    $location = $test->id();

                    if ($test instanceof Event\Code\TestMethod) {
                        $location .= ' (' . $test->file() . ':' . $test->line() . ')';
                    }

                    $record .= $this->compactRenderer->body('Triggered by: ' . $location);
                }
            }

            $this->printer->print($record);
        }
    }

    private function printRiskyTests(TestRunner\TestResult\TestResult $result): void
    {
        foreach ($result->testConsideredRiskyEvents() as $events) {
            $this->printTestRecord(
                'RISKY',
                $events[0]->test(),
                ...\array_map(static function (Event\Test\ConsideredRisky $event): string {
                    return $event->message();
                }, $events),
            );
        }
    }

    private function printIncompleteTests(TestRunner\TestResult\TestResult $result): void
    {
        foreach ($result->testMarkedIncompleteEvents() as $event) {
            $this->printer->print($this->compactRenderer->header(
                'INCOMPLETE',
                $this->eventRenderer->nameOfTest($event->test()),
            ) . $this->compactRenderer->body(\trim($event->throwable()->description())));
        }
    }

    private function printSkippedTests(TestRunner\TestResult\TestResult $result): void
    {
        foreach ($result->testSkippedEvents() as $event) {
            $record = $this->compactRenderer->header(
                'SKIPPED',
                $this->eventRenderer->nameOfTest($event->test()),
            );

            if ('' !== $event->message()) {
                $record .= $this->compactRenderer->body($event->message());
            }

            $this->printer->print($record);
        }
    }

    private static function count(
        int $count,
        string $singular,
        string $plural,
    ): string {
        if (1 === $count) {
            return \sprintf(
                '%d %s',
                $count,
                $singular,
            );
        }

        return \sprintf(
            '%d %s',
            $count,
            $plural,
        );
    }

    /**
     * phpunit/phpunit counts notices triggered by itself only since 12.1.0.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/12.1.0/src/Runner/TestResult/TestResult.php
     */
    private static function numberOfPhpunitNotices(TestRunner\TestResult\TestResult $result): int
    {
        if (!\method_exists($result, 'numberOfPhpunitNotices')) {
            return 0;
        }

        $numberOfPhpunitNotices = $result->numberOfPhpunitNotices();

        if (!\is_int($numberOfPhpunitNotices)) {
            return 0;
        }

        return $numberOfPhpunitNotices;
    }

    /**
     * phpunit/phpunit counts tests skipped because their test suite was skipped only since 11.5.51, 12.5.9, and 13.0.0.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/13.0.0/src/Runner/TestResult/TestResult.php
     */
    private static function numberOfTestSkippedByTestSuiteSkippedEvents(TestRunner\TestResult\TestResult $result): int
    {
        if (!\method_exists($result, 'numberOfTestSkippedByTestSuiteSkippedEvents')) {
            return 0;
        }

        $numberOfTestSkippedByTestSuiteSkippedEvents = $result->numberOfTestSkippedByTestSuiteSkippedEvents();

        if (!\is_int($numberOfTestSkippedByTestSuiteSkippedEvents)) {
            return 0;
        }

        return $numberOfTestSkippedByTestSuiteSkippedEvents;
    }

    /**
     * phpunit/phpunit provides Issue::triggeredInTest() only since 11.0.0, so this repeats its implementation.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/Runner/TestResult/Issue.php
     */
    private static function triggeredInTest(TestRunner\TestResult\Issues\Issue $issue): bool
    {
        $triggeringTests = $issue->triggeringTests();

        if (1 !== \count($triggeringTests)) {
            return false;
        }

        $triggeringTest = \array_values($triggeringTests)[0];

        return $issue->file() === $triggeringTest['test']->file();
    }
}
