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

namespace Ergebnis\PHPUnit\AgentReporter\Report;

use PHPUnit\Event;
use PHPUnit\TestRunner;

/**
 * @internal
 */
final class TestResultTranslator
{
    public function erroredTestListFrom(TestRunner\TestResult\TestResult $testResult): ErroredTestList
    {
        $erroredTests = [];

        foreach ($testResult->testErroredEvents() as $event) {
            if ($event instanceof Event\Test\Errored) {
                $test = $event->test();

                $line = 0;

                if ($test->isTestMethod()) {
                    /** @var Event\Code\TestMethod $test */
                    $line = $test->line();
                }

                $erroredTests[] = ErroredTest::create(
                    TestIdentifier::fromString($test->id()),
                    File::fromString($test->file()),
                    Line::fromInt($line),
                    Message::fromString($event->throwable()->message()),
                );

                continue;
            }

            if ($event instanceof Event\Test\BeforeFirstTestMethodErrored) {
                $erroredTests[] = ErroredTest::create(
                    TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $event->calledMethod()->className(),
                        $event->calledMethod()->methodName(),
                    )),
                    File::fromString($event->testClassName()),
                    Line::fromInt(0),
                    Message::fromString($event->throwable()->message()),
                );

                continue;
            }

            if ($event instanceof Event\Test\AfterLastTestMethodErrored) {
                $erroredTests[] = ErroredTest::create(
                    TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $event->calledMethod()->className(),
                        $event->calledMethod()->methodName(),
                    )),
                    File::fromString($event->testClassName()),
                    Line::fromInt(0),
                    Message::fromString($event->throwable()->message()),
                );
            }
        }

        return ErroredTestList::create(...$erroredTests);
    }

    public function failedTestListFrom(TestRunner\TestResult\TestResult $testResult): FailedTestList
    {
        $failedTests = [];

        foreach ($testResult->testFailedEvents() as $event) {
            $test = $event->test();

            $line = 0;

            if ($test->isTestMethod()) {
                /** @var Event\Code\TestMethod $test */
                $line = $test->line();
            }

            $comparisonFailure = null;

            if ($event->hasComparisonFailure()) {
                $eventComparisonFailure = $event->comparisonFailure();

                $comparisonFailure = ComparisonFailure::create(
                    Actual::fromString($eventComparisonFailure->actual()),
                    Diff::fromString($eventComparisonFailure->diff()),
                    Expected::fromString($eventComparisonFailure->expected()),
                );
            }

            $failedTests[] = FailedTest::create(
                TestIdentifier::fromString($test->id()),
                File::fromString($test->file()),
                Line::fromInt($line),
                Message::fromString($event->throwable()->message()),
                $comparisonFailure,
            );
        }

        return FailedTestList::create(...$failedTests);
    }

    public function incompleteTestListFrom(TestRunner\TestResult\TestResult $testResult): IncompleteTestList
    {
        $incompleteTests = [];

        foreach ($testResult->testMarkedIncompleteEvents() as $event) {
            $test = $event->test();

            $line = 0;

            if ($test->isTestMethod()) {
                /** @var Event\Code\TestMethod $test */
                $line = $test->line();
            }

            $incompleteTests[] = IncompleteTest::create(
                TestIdentifier::fromString($test->id()),
                File::fromString($test->file()),
                Line::fromInt($line),
                Message::fromString($event->throwable()->message()),
            );
        }

        return IncompleteTestList::create(...$incompleteTests);
    }

    public function skippedTestListFrom(TestRunner\TestResult\TestResult $testResult): SkippedTestList
    {
        $skippedTests = [];

        foreach ($testResult->testSkippedEvents() as $event) {
            $test = $event->test();

            $line = 0;

            if ($test->isTestMethod()) {
                /** @var Event\Code\TestMethod $test */
                $line = $test->line();
            }

            $skippedTests[] = SkippedTest::create(
                TestIdentifier::fromString($test->id()),
                File::fromString($test->file()),
                Line::fromInt($line),
                Message::fromString($event->message()),
            );
        }

        return SkippedTestList::create(...$skippedTests);
    }

    public function riskyTestListFrom(TestRunner\TestResult\TestResult $testResult): RiskyTestList
    {
        $riskyTests = [];

        foreach ($testResult->testConsideredRiskyEvents() as $eventsForTest) {
            foreach ($eventsForTest as $event) {
                $test = $event->test();

                $line = 0;

                if ($test->isTestMethod()) {
                    /** @var Event\Code\TestMethod $test */
                    $line = $test->line();
                }

                $riskyTests[] = RiskyTest::create(
                    TestIdentifier::fromString($test->id()),
                    File::fromString($test->file()),
                    Line::fromInt($line),
                    Message::fromString($event->message()),
                );
            }
        }

        return RiskyTestList::create(...$riskyTests);
    }

    public function deprecationListFrom(TestRunner\TestResult\TestResult $testResult): DeprecationList
    {
        $deprecations = [];

        foreach ($testResult->deprecations() as $issue) {
            $deprecations[] = self::deprecationFrom($issue);
        }

        foreach ($testResult->phpDeprecations() as $issue) {
            $deprecations[] = self::deprecationFrom($issue);
        }

        return DeprecationList::create(...$deprecations);
    }

    public function noticeListFrom(TestRunner\TestResult\TestResult $testResult): NoticeList
    {
        $notices = [];

        foreach ($testResult->notices() as $issue) {
            $notices[] = self::noticeFrom($issue);
        }

        foreach ($testResult->phpNotices() as $issue) {
            $notices[] = self::noticeFrom($issue);
        }

        return NoticeList::create(...$notices);
    }

    public function warningListFrom(TestRunner\TestResult\TestResult $testResult): WarningList
    {
        $warnings = [];

        foreach ($testResult->warnings() as $issue) {
            $warnings[] = self::warningFrom($issue);
        }

        foreach ($testResult->phpWarnings() as $issue) {
            $warnings[] = self::warningFrom($issue);
        }

        return WarningList::create(...$warnings);
    }

    private static function deprecationFrom(TestRunner\TestResult\Issues\Issue $issue): Deprecation
    {
        return Deprecation::create(
            File::fromString($issue->file()),
            Line::fromInt($issue->line()),
            Message::fromString($issue->description()),
            self::triggeredByFrom($issue),
        );
    }

    private static function noticeFrom(TestRunner\TestResult\Issues\Issue $issue): Notice
    {
        return Notice::create(
            File::fromString($issue->file()),
            Line::fromInt($issue->line()),
            Message::fromString($issue->description()),
            self::triggeredByFrom($issue),
        );
    }

    private static function warningFrom(TestRunner\TestResult\Issues\Issue $issue): Warning
    {
        return Warning::create(
            File::fromString($issue->file()),
            Line::fromInt($issue->line()),
            Message::fromString($issue->description()),
            self::triggeredByFrom($issue),
        );
    }

    private static function triggeredByFrom(TestRunner\TestResult\Issues\Issue $issue): TestIdentifierList
    {
        $testIdentifiers = [];

        foreach ($issue->triggeringTests() as $triggeringTest) {
            $testIdentifiers[] = TestIdentifier::fromString($triggeringTest['test']->id());
        }

        return TestIdentifierList::create(...$testIdentifiers);
    }
}
