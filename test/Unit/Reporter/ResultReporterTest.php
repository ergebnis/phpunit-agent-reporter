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
use PHPUnit\TestRunner;

/**
 * The constructors of TestResult and of the events differ between major versions of phpunit/phpunit, so this test runs on the version that the lock file pins and that mutation testing uses. The end-to-end tests cover all supported versions.
 */
#[Framework\Attributes\CoversClass(Reporter\ResultReporter::class)]
#[Framework\Attributes\RequiresPhpunit('< 11.0.0')]
#[Framework\Attributes\UsesClass(Output\Printer::class)]
#[Framework\Attributes\UsesClass(Output\CompactRenderer::class)]
#[Framework\Attributes\UsesClass(Reporter\EventRenderer::class)]
#[Framework\Attributes\UsesClass(Output\Sanitizer::class)]
final class ResultReporterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testPrintPrintsNoTestsExecutedWhenNoTestsHaveBeenRun(): void
    {
        $output = self::outputStream();

        $resultReporter = self::resultReporter(
            $output,
            true,
        );

        $resultReporter->print(self::testResult([
            'numberOfTestsRun' => 0,
        ]));

        self::assertOutputIsIdenticalTo('No tests executed!' . \PHP_EOL, $output);
    }

    public function testPrintPrintsSummaryLineWithSingularCountsWhenTestsWereSuccessful(): void
    {
        $output = self::outputStream();

        $resultReporter = self::resultReporter(
            $output,
            true,
        );

        $resultReporter->print(self::testResult([
            'numberOfAssertions' => 1,
            'numberOfTestsRun' => 1,
        ]));

        self::assertOutputIsIdenticalTo('OK (1 test, 1 assertion)' . \PHP_EOL, $output);
    }

    public function testPrintPrintsSummaryLineWithFailuresWhenTestsFailed(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::outputStream();

        $resultReporter = self::resultReporter(
            $output,
            true,
        );

        $resultReporter->print(self::testResult([
            'numberOfAssertions' => 3,
            'numberOfTestsRun' => 2,
            'testFailedEvents' => [
                new Event\Test\Failed(
                    self::telemetryInfo(),
                    $test,
                    self::throwable(),
                    null,
                ),
            ],
        ]));

        self::assertOutputIsIdenticalTo('FAILURES (2 tests, 3 assertions, 1 failure)' . \PHP_EOL, $output);
    }

    public function testPrintPrintsSummaryLineWithErrorsWhenTestTriggeredPhpunitError(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));
        $phpunitError = self::faker()->sentence();

        $output = self::outputStream();

        $resultReporter = self::resultReporter(
            $output,
            true,
        );

        $resultReporter->print(self::testResult([
            'numberOfAssertions' => 1,
            'numberOfTestsRun' => 1,
            'testTriggeredPhpunitErrorEvents' => [
                $test->id() => [
                    new Event\Test\PhpunitErrorTriggered(
                        self::telemetryInfo(),
                        $test,
                        $phpunitError,
                    ),
                ],
            ],
        ]));

        $expected = 'ERRORS (1 test, 1 assertion, 1 error)' . \PHP_EOL
            . \PHP_EOL . '--- PHPUNIT ERROR: ' . $test->nameWithClass() . \PHP_EOL
            . $phpunitError . \PHP_EOL;

        self::assertOutputIsIdenticalTo($expected, $output);
    }

    public function testPrintPrintsSummaryLineAndRecordsThatAreAlwaysDisplayedWhenDetailsAreNotDisplayed(): void
    {
        $output = self::outputStream();

        $resultReporter = self::resultReporter(
            $output,
            false,
        );

        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $phpt = self::phpt();
        $values = self::values();

        $resultReporter->print(self::testResultWithEverything(
            $test,
            $phpt,
            $values,
        ));

        $name = $test->nameWithClass();

        $expected = <<<TXT
ERRORS (5 tests, 7 assertions, 4 errors, 2 failures, 2 deprecations, 3 PHPUnit deprecations, 2 warnings, 5 PHPUnit warnings, 2 notices, 2 skipped, 1 incomplete, 2 risky)

--- PHPUNIT ERROR: {$name}
{$values['phpunitError']}

--- PHPUNIT TEST RUNNER WARNING
{$values['testRunnerWarning']}

--- PHPUNIT TEST RUNNER WARNING
{$values['otherTestRunnerWarning']}

--- PHPUNIT WARNING: {$name}
{$values['phpunitWarning']}
{$values['otherPhpunitWarning']}

--- PHPUNIT WARNING: {$phpt->name()}
{$values['phpunitWarningInPhpt']}

--- RISKY: {$name}
{$values['risky']}
{$values['otherRisky']}

--- RISKY: {$phpt->name()}
{$values['riskyPhpt']}

TXT;

        self::assertOutputIsIdenticalTo(self::withPlatformLineEndings($expected), $output);
    }

    public function testPrintPrintsSummaryLineAndRecordsWhenDetailsAreDisplayed(): void
    {
        $output = self::outputStream();

        $resultReporter = self::resultReporter(
            $output,
            true,
        );

        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $phpt = self::phpt();
        $values = self::values();

        $resultReporter->print(self::testResultWithEverything(
            $test,
            $phpt,
            $values,
        ));

        $name = $test->nameWithClass();
        $location = $test->id() . ' (' . $test->file() . ':' . $test->line() . ')';

        $expected = <<<TXT
ERRORS (5 tests, 7 assertions, 4 errors, 2 failures, 2 deprecations, 3 PHPUnit deprecations, 2 warnings, 5 PHPUnit warnings, 2 notices, 2 skipped, 1 incomplete, 2 risky)

--- PHPUNIT ERROR: {$name}
{$values['phpunitError']}

--- PHPUNIT TEST RUNNER WARNING
{$values['testRunnerWarning']}

--- PHPUNIT TEST RUNNER WARNING
{$values['otherTestRunnerWarning']}

--- PHPUNIT TEST RUNNER DEPRECATION
{$values['testRunnerDeprecation']}

--- PHPUNIT TEST RUNNER DEPRECATION
{$values['testRunnerDeprecation']}

--- PHPUNIT WARNING: {$name}
{$values['phpunitWarning']}
{$values['otherPhpunitWarning']}

--- PHPUNIT WARNING: {$phpt->name()}
{$values['phpunitWarningInPhpt']}

--- PHPUNIT DEPRECATION: {$name}
{$values['phpunitDeprecation']}

--- DEPRECATION: {$values['phpDeprecationFile']}:{$values['phpDeprecationLine']}
{$values['phpDeprecation']}
Triggered by: {$location}

--- DEPRECATION: {$test->file()}:{$values['deprecationLine']}
{$values['deprecation']}

--- WARNING: {$values['phpWarningFile']}:{$values['phpWarningLine']}
{$values['phpWarning']}
Triggered by: {$location}

--- WARNING: {$values['warningFile']}:{$values['warningLine']}
{$values['warning']}
Triggered by: {$phpt->id()}
Triggered by: {$location}

--- NOTICE: {$values['phpNoticeFile']}:{$values['phpNoticeLine']}
{$values['phpNotice']}
Triggered by: {$location}

--- NOTICE: {$values['noticeFile']}:{$values['noticeLine']}
{$values['notice']}
Triggered by: {$location}

--- ERROR: {$values['errorFile']}:{$values['errorLine']}
{$values['error']}
Triggered by: {$location}

--- RISKY: {$name}
{$values['risky']}
{$values['otherRisky']}

--- RISKY: {$phpt->name()}
{$values['riskyPhpt']}

--- INCOMPLETE: {$name}
{$values['incomplete']}

--- SKIPPED: {$name}
{$values['skipped']}

--- SKIPPED: {$name}

TXT;

        self::assertOutputIsIdenticalTo(self::withPlatformLineEndings($expected), $output);
    }

    /**
     * @param resource $output
     */
    private static function resultReporter(
        $output,
        bool $displayDetails,
    ): Reporter\ResultReporter {
        $compactRenderer = new Output\CompactRenderer(new Output\Sanitizer());

        return new Reporter\ResultReporter(
            new Output\Printer($output),
            $compactRenderer,
            new Reporter\EventRenderer($compactRenderer),
            $displayDetails,
            $displayDetails,
            $displayDetails,
            $displayDetails,
            $displayDetails,
            $displayDetails,
            $displayDetails,
            $displayDetails,
        );
    }

    /**
     * @param array<string, int|string> $values
     */
    private static function testResultWithEverything(
        Event\Code\TestMethod $test,
        Event\Code\Phpt $phpt,
        array $values,
    ): TestRunner\TestResult\TestResult {
        $info = self::telemetryInfo();

        $warning = TestRunner\TestResult\Issues\Issue::from(
            $values['warningFile'],
            $values['warningLine'],
            $values['warning'],
            $test,
        );

        $warning->triggeredBy($phpt);

        return self::testResult([
            'deprecations' => [
                TestRunner\TestResult\Issues\Issue::from(
                    $test->file(),
                    $values['deprecationLine'],
                    $values['deprecation'],
                    $test,
                ),
            ],
            'errors' => [
                TestRunner\TestResult\Issues\Issue::from(
                    $values['errorFile'],
                    $values['errorLine'],
                    $values['error'],
                    $test,
                ),
            ],
            'notices' => [
                TestRunner\TestResult\Issues\Issue::from(
                    $values['noticeFile'],
                    $values['noticeLine'],
                    $values['notice'],
                    $test,
                ),
            ],
            'numberOfAssertions' => 7,
            'numberOfTestsRun' => 5,
            'phpDeprecations' => [
                TestRunner\TestResult\Issues\Issue::from(
                    $values['phpDeprecationFile'],
                    $values['phpDeprecationLine'],
                    $values['phpDeprecation'] . "\n",
                    $test,
                ),
            ],
            'phpNotices' => [
                TestRunner\TestResult\Issues\Issue::from(
                    $values['phpNoticeFile'],
                    $values['phpNoticeLine'],
                    $values['phpNotice'],
                    $test,
                ),
            ],
            'phpWarnings' => [
                TestRunner\TestResult\Issues\Issue::from(
                    $values['phpWarningFile'],
                    $values['phpWarningLine'],
                    $values['phpWarning'],
                    $test,
                ),
            ],
            'testConsideredRiskyEvents' => [
                $test->id() => [
                    new Event\Test\ConsideredRisky(
                        $info,
                        $test,
                        $values['risky'],
                    ),
                    new Event\Test\ConsideredRisky(
                        $info,
                        $test,
                        $values['otherRisky'],
                    ),
                ],
                $phpt->id() => [
                    new Event\Test\ConsideredRisky(
                        $info,
                        $phpt,
                        $values['riskyPhpt'],
                    ),
                ],
            ],
            'testErroredEvents' => [
                new Event\Test\Errored(
                    $info,
                    $test,
                    self::throwable(),
                ),
                new Event\Test\Errored(
                    $info,
                    $test,
                    self::throwable(),
                ),
            ],
            'testFailedEvents' => [
                new Event\Test\Failed(
                    $info,
                    $test,
                    self::throwable(),
                    null,
                ),
                new Event\Test\Failed(
                    $info,
                    $test,
                    self::throwable(),
                    null,
                ),
            ],
            'testMarkedIncompleteEvents' => [
                new Event\Test\MarkedIncomplete(
                    $info,
                    $test,
                    new Event\Code\Throwable(
                        Framework\IncompleteTestError::class,
                        $values['incomplete'],
                        $values['incomplete'] . "\n",
                        '',
                        null,
                    ),
                ),
            ],
            'testRunnerTriggeredDeprecationEvents' => [
                new Event\TestRunner\DeprecationTriggered(
                    $info,
                    $values['testRunnerDeprecation'],
                ),
                new Event\TestRunner\DeprecationTriggered(
                    $info,
                    $values['testRunnerDeprecation'],
                ),
            ],
            'testRunnerTriggeredWarningEvents' => [
                new Event\TestRunner\WarningTriggered(
                    $info,
                    $values['testRunnerWarning'] . "\n",
                ),
                new Event\TestRunner\WarningTriggered(
                    $info,
                    $values['testRunnerWarning'] . "\n",
                ),
                new Event\TestRunner\WarningTriggered(
                    $info,
                    $values['otherTestRunnerWarning'],
                ),
            ],
            'testSkippedEvents' => [
                new Event\Test\Skipped(
                    $info,
                    $test,
                    $values['skipped'],
                ),
                new Event\Test\Skipped(
                    $info,
                    $test,
                    '',
                ),
            ],
            'testTriggeredPhpunitDeprecationEvents' => [
                $test->id() => [
                    new Event\Test\PhpunitDeprecationTriggered(
                        $info,
                        $test,
                        $values['phpunitDeprecation'] . "\n",
                    ),
                ],
            ],
            'testTriggeredPhpunitErrorEvents' => [
                $test->id() => [
                    new Event\Test\PhpunitErrorTriggered(
                        $info,
                        $test,
                        $values['phpunitError'] . "\n",
                    ),
                ],
            ],
            'testTriggeredPhpunitWarningEvents' => [
                $test->id() => [
                    new Event\Test\PhpunitWarningTriggered(
                        $info,
                        $test,
                        $values['phpunitWarning'] . "\n",
                    ),
                    new Event\Test\PhpunitWarningTriggered(
                        $info,
                        $test,
                        $values['otherPhpunitWarning'],
                    ),
                ],
                $phpt->id() => [
                    new Event\Test\PhpunitWarningTriggered(
                        $info,
                        $phpt,
                        $values['phpunitWarningInPhpt'],
                    ),
                ],
            ],
            'warnings' => [
                $warning,
            ],
        ]);
    }

    /**
     * @return array<string, int|string>
     */
    private static function values(): array
    {
        $faker = self::faker();

        $values = [];

        foreach ([
            'deprecation',
            'error',
            'incomplete',
            'notice',
            'otherPhpunitWarning',
            'otherRisky',
            'otherTestRunnerWarning',
            'phpDeprecation',
            'phpNotice',
            'phpWarning',
            'phpunitDeprecation',
            'phpunitError',
            'phpunitWarning',
            'phpunitWarningInPhpt',
            'risky',
            'riskyPhpt',
            'skipped',
            'testRunnerDeprecation',
            'testRunnerWarning',
            'warning',
        ] as $key) {
            $values[$key] = $faker->unique()->sentence();
        }

        foreach ([
            'deprecation',
            'error',
            'notice',
            'phpDeprecation',
            'phpNotice',
            'phpWarning',
            'warning',
        ] as $key) {
            $values[$key . 'File'] = '/' . $faker->unique()->word() . '.php';
            $values[$key . 'Line'] = $faker->numberBetween(1, 1000);
        }

        return $values;
    }

    /**
     * @param array<string, mixed> $values
     */
    private static function testResult(array $values): TestRunner\TestResult\TestResult
    {
        $defaults = [
            'deprecations' => [],
            'errors' => [],
            'notices' => [],
            'numberOfAssertions' => 0,
            'numberOfIssuesIgnoredByBaseline' => 0,
            'numberOfTests' => 0,
            'numberOfTestsRun' => 0,
            'phpDeprecations' => [],
            'phpNotices' => [],
            'phpWarnings' => [],
            'testConsideredRiskyEvents' => [],
            'testErroredEvents' => [],
            'testFailedEvents' => [],
            'testMarkedIncompleteEvents' => [],
            'testRunnerTriggeredDeprecationEvents' => [],
            'testRunnerTriggeredWarningEvents' => [],
            'testSkippedEvents' => [],
            'testSuiteSkippedEvents' => [],
            'testTriggeredPhpunitDeprecationEvents' => [],
            'testTriggeredPhpunitErrorEvents' => [],
            'testTriggeredPhpunitWarningEvents' => [],
            'warnings' => [],
        ];

        $arguments = \array_merge(
            $defaults,
            $values,
        );

        if (!\array_key_exists('numberOfTests', $values)) {
            $arguments['numberOfTests'] = $arguments['numberOfTestsRun'];
        }

        return new TestRunner\TestResult\TestResult(
            $arguments['numberOfTests'],
            $arguments['numberOfTestsRun'],
            $arguments['numberOfAssertions'],
            $arguments['testErroredEvents'],
            $arguments['testFailedEvents'],
            $arguments['testConsideredRiskyEvents'],
            $arguments['testSuiteSkippedEvents'],
            $arguments['testSkippedEvents'],
            $arguments['testMarkedIncompleteEvents'],
            $arguments['testTriggeredPhpunitDeprecationEvents'],
            $arguments['testTriggeredPhpunitErrorEvents'],
            $arguments['testTriggeredPhpunitWarningEvents'],
            $arguments['testRunnerTriggeredDeprecationEvents'],
            $arguments['testRunnerTriggeredWarningEvents'],
            $arguments['errors'],
            $arguments['deprecations'],
            $arguments['notices'],
            $arguments['warnings'],
            $arguments['phpDeprecations'],
            $arguments['phpNotices'],
            $arguments['phpWarnings'],
            $arguments['numberOfIssuesIgnoredByBaseline'],
        );
    }

    private static function withPlatformLineEndings(string $text): string
    {
        return \str_replace(
            "\n",
            \PHP_EOL,
            $text,
        );
    }

    private static function phpt(): Event\Code\Phpt
    {
        return new Event\Code\Phpt('/' . self::faker()->word() . '.phpt');
    }

    private static function telemetryInfo(): Event\Telemetry\Info
    {
        $snapshot = new Event\Telemetry\Snapshot(
            Event\Telemetry\HRTime::fromSecondsAndNanoseconds(
                0,
                0,
            ),
            Event\Telemetry\MemoryUsage::fromBytes(0),
            Event\Telemetry\MemoryUsage::fromBytes(0),
            new Event\Telemetry\GarbageCollectorStatus(
                0,
                0,
                0,
                0,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ),
        );

        return new Event\Telemetry\Info(
            $snapshot,
            Event\Telemetry\Duration::fromSecondsAndNanoseconds(
                0,
                0,
            ),
            Event\Telemetry\MemoryUsage::fromBytes(0),
            Event\Telemetry\Duration::fromSecondsAndNanoseconds(
                0,
                0,
            ),
            Event\Telemetry\MemoryUsage::fromBytes(0),
        );
    }

    private static function throwable(): Event\Code\Throwable
    {
        $faker = self::faker();

        return new Event\Code\Throwable(
            \RuntimeException::class,
            $faker->sentence(),
            $faker->sentence(),
            '',
            null,
        );
    }
}
