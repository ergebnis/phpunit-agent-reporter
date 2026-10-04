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
use PHPUnit\TestRunner;

/**
 * The constructors of TestResult and of the events differ between major versions of phpunit/phpunit, so this test runs on the version that the lock file pins and that mutation testing uses. The end-to-end tests cover all supported versions.
 */
#[Framework\Attributes\CoversClass(Output\ResultPrinter::class)]
#[Framework\Attributes\RequiresPhpunit('< 11.0.0')]
#[Framework\Attributes\UsesClass(Output\Printer::class)]
#[Framework\Attributes\UsesClass(Output\Renderer::class)]
#[Framework\Attributes\UsesClass(Output\Sanitizer::class)]
final class ResultPrinterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testPrintPrintsNoTestsExecutedWhenNoTestsHaveBeenRun(): void
    {
        $output = self::memoryStream();

        $resultPrinter = self::resultPrinter(
            $output,
            true,
        );

        $resultPrinter->print(self::testResult([
            'numberOfTestsRun' => 0,
        ]));

        self::assertSame('No tests executed!' . \PHP_EOL, self::contentsOf($output));
    }

    public function testPrintPrintsSummaryLineWithSingularCountsWhenTestsWereSuccessful(): void
    {
        $output = self::memoryStream();

        $resultPrinter = self::resultPrinter(
            $output,
            true,
        );

        $resultPrinter->print(self::testResult([
            'numberOfAssertions' => 1,
            'numberOfTestsRun' => 1,
        ]));

        self::assertSame('OK (1 test, 1 assertion)' . \PHP_EOL, self::contentsOf($output));
    }

    public function testPrintPrintsSummaryLineWithFailuresWhenTestsFailed(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::memoryStream();

        $resultPrinter = self::resultPrinter(
            $output,
            true,
        );

        $resultPrinter->print(self::testResult([
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

        self::assertSame('FAILURES (2 tests, 3 assertions, 1 failure)' . \PHP_EOL, self::contentsOf($output));
    }

    public function testPrintPrintsSummaryLineWithErrorsWhenTestTriggeredPhpunitError(): void
    {
        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $output = self::memoryStream();

        $resultPrinter = self::resultPrinter(
            $output,
            true,
        );

        $resultPrinter->print(self::testResult([
            'numberOfAssertions' => 1,
            'numberOfTestsRun' => 1,
            'testTriggeredPhpunitErrorEvents' => [
                $test->id() => [
                    new Event\Test\PhpunitErrorTriggered(
                        self::telemetryInfo(),
                        $test,
                        'phpunit error',
                    ),
                ],
            ],
        ]));

        $expected = 'ERRORS (1 test, 1 assertion, 1 error)' . \PHP_EOL
            . \PHP_EOL . '--- PHPUNIT ERROR: ' . $test->nameWithClass() . \PHP_EOL
            . 'phpunit error' . \PHP_EOL;

        self::assertSame($expected, self::contentsOf($output));
    }

    public function testPrintPrintsSummaryLineAndRecordsThatAreAlwaysDisplayedWhenDetailsAreNotDisplayed(): void
    {
        $output = self::memoryStream();

        $resultPrinter = self::resultPrinter(
            $output,
            false,
        );

        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $resultPrinter->print(self::testResultWithEverything($test));

        $name = $test->nameWithClass();

        $expected = <<<TXT
ERRORS (5 tests, 7 assertions, 4 errors, 2 failures, 2 deprecations, 3 PHPUnit deprecations, 2 warnings, 5 PHPUnit warnings, 2 notices, 2 skipped, 1 incomplete, 2 risky)

--- PHPUNIT ERROR: {$name}
phpunit error

--- PHPUNIT TEST RUNNER WARNING
test runner warning

--- PHPUNIT TEST RUNNER WARNING
other test runner warning

--- PHPUNIT WARNING: {$name}
phpunit warning
other phpunit warning

--- PHPUNIT WARNING: /test.phpt
phpunit warning in phpt

--- RISKY: {$name}
risky
other risky

--- RISKY: /test.phpt
risky phpt

TXT;

        self::assertSame(self::withPlatformLineEndings($expected), self::contentsOf($output));
    }

    public function testPrintPrintsSummaryLineAndRecordsWhenDetailsAreDisplayed(): void
    {
        $output = self::memoryStream();

        $resultPrinter = self::resultPrinter(
            $output,
            true,
        );

        $test = self::testMethod(Event\TestData\TestDataCollection::fromArray([]));

        $resultPrinter->print(self::testResultWithEverything($test));

        $name = $test->nameWithClass();
        $location = $test->id() . ' (' . $test->file() . ':' . $test->line() . ')';

        $expected = <<<TXT
ERRORS (5 tests, 7 assertions, 4 errors, 2 failures, 2 deprecations, 3 PHPUnit deprecations, 2 warnings, 5 PHPUnit warnings, 2 notices, 2 skipped, 1 incomplete, 2 risky)

--- PHPUNIT ERROR: {$name}
phpunit error

--- PHPUNIT TEST RUNNER WARNING
test runner warning

--- PHPUNIT TEST RUNNER WARNING
other test runner warning

--- PHPUNIT TEST RUNNER DEPRECATION
test runner deprecation

--- PHPUNIT TEST RUNNER DEPRECATION
test runner deprecation

--- PHPUNIT WARNING: {$name}
phpunit warning
other phpunit warning

--- PHPUNIT WARNING: /test.phpt
phpunit warning in phpt

--- PHPUNIT DEPRECATION: {$name}
phpunit deprecation

--- DEPRECATION: /php-deprecation.php:1
php deprecation
Triggered by: {$location}

--- DEPRECATION: {$test->file()}:2
deprecation

--- WARNING: /php-warning.php:3
php warning
Triggered by: {$location}

--- WARNING: /warning.php:4
warning
Triggered by: /test.phpt
Triggered by: {$location}

--- NOTICE: /php-notice.php:5
php notice
Triggered by: {$location}

--- NOTICE: /notice.php:6
notice
Triggered by: {$location}

--- ERROR: /error.php:7
error
Triggered by: {$location}

--- RISKY: {$name}
risky
other risky

--- RISKY: /test.phpt
risky phpt

--- INCOMPLETE: {$name}
incomplete

--- SKIPPED: {$name}
skipped

--- SKIPPED: {$name}

TXT;

        self::assertSame(self::withPlatformLineEndings($expected), self::contentsOf($output));
    }

    /**
     * @param resource $output
     */
    private static function resultPrinter(
        $output,
        bool $displayDetails,
    ): Output\ResultPrinter {
        return new Output\ResultPrinter(
            new Output\Printer($output),
            new Output\Renderer(new Output\Sanitizer()),
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

    private static function testResultWithEverything(Event\Code\TestMethod $test): TestRunner\TestResult\TestResult
    {
        $info = self::telemetryInfo();
        $phpt = self::phpt();

        $warning = TestRunner\TestResult\Issues\Issue::from(
            '/warning.php',
            4,
            'warning',
            $test,
        );

        $warning->triggeredBy($phpt);

        return self::testResult([
            'deprecations' => [
                TestRunner\TestResult\Issues\Issue::from(
                    $test->file(),
                    2,
                    'deprecation',
                    $test,
                ),
            ],
            'errors' => [
                TestRunner\TestResult\Issues\Issue::from(
                    '/error.php',
                    7,
                    'error',
                    $test,
                ),
            ],
            'notices' => [
                TestRunner\TestResult\Issues\Issue::from(
                    '/notice.php',
                    6,
                    'notice',
                    $test,
                ),
            ],
            'numberOfAssertions' => 7,
            'numberOfTestsRun' => 5,
            'phpDeprecations' => [
                TestRunner\TestResult\Issues\Issue::from(
                    '/php-deprecation.php',
                    1,
                    "php deprecation\n",
                    $test,
                ),
            ],
            'phpNotices' => [
                TestRunner\TestResult\Issues\Issue::from(
                    '/php-notice.php',
                    5,
                    'php notice',
                    $test,
                ),
            ],
            'phpWarnings' => [
                TestRunner\TestResult\Issues\Issue::from(
                    '/php-warning.php',
                    3,
                    'php warning',
                    $test,
                ),
            ],
            'testConsideredRiskyEvents' => [
                $test->id() => [
                    new Event\Test\ConsideredRisky(
                        $info,
                        $test,
                        'risky',
                    ),
                    new Event\Test\ConsideredRisky(
                        $info,
                        $test,
                        'other risky',
                    ),
                ],
                $phpt->id() => [
                    new Event\Test\ConsideredRisky(
                        $info,
                        $phpt,
                        'risky phpt',
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
                        'incomplete',
                        "incomplete\n",
                        '',
                        null,
                    ),
                ),
            ],
            'testRunnerTriggeredDeprecationEvents' => [
                new Event\TestRunner\DeprecationTriggered(
                    $info,
                    'test runner deprecation',
                ),
                new Event\TestRunner\DeprecationTriggered(
                    $info,
                    'test runner deprecation',
                ),
            ],
            'testRunnerTriggeredWarningEvents' => [
                new Event\TestRunner\WarningTriggered(
                    $info,
                    "test runner warning\n",
                ),
                new Event\TestRunner\WarningTriggered(
                    $info,
                    "test runner warning\n",
                ),
                new Event\TestRunner\WarningTriggered(
                    $info,
                    'other test runner warning',
                ),
            ],
            'testSkippedEvents' => [
                new Event\Test\Skipped(
                    $info,
                    $test,
                    'skipped',
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
                        "phpunit deprecation\n",
                    ),
                ],
            ],
            'testTriggeredPhpunitErrorEvents' => [
                $test->id() => [
                    new Event\Test\PhpunitErrorTriggered(
                        $info,
                        $test,
                        "phpunit error\n",
                    ),
                ],
            ],
            'testTriggeredPhpunitWarningEvents' => [
                $test->id() => [
                    new Event\Test\PhpunitWarningTriggered(
                        $info,
                        $test,
                        "phpunit warning\n",
                    ),
                    new Event\Test\PhpunitWarningTriggered(
                        $info,
                        $test,
                        'other phpunit warning',
                    ),
                ],
                $phpt->id() => [
                    new Event\Test\PhpunitWarningTriggered(
                        $info,
                        $phpt,
                        'phpunit warning in phpt',
                    ),
                ],
            ],
            'warnings' => [
                $warning,
            ],
        ]);
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
        return new Event\Code\Phpt('/test.phpt');
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
        return new Event\Code\Throwable(
            \RuntimeException::class,
            'message',
            'description',
            '',
            null,
        );
    }
}
