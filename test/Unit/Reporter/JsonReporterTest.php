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

use Ergebnis\PHPUnit\AgentReporter\Report;
use Ergebnis\PHPUnit\AgentReporter\Reporter;
use Ergebnis\PHPUnit\AgentReporter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Reporter\JsonReporter::class)]
#[Framework\Attributes\UsesClass(Report\Actual::class)]
#[Framework\Attributes\UsesClass(Report\ComparisonFailure::class)]
#[Framework\Attributes\UsesClass(Report\Count::class)]
#[Framework\Attributes\UsesClass(Report\Deprecation::class)]
#[Framework\Attributes\UsesClass(Report\DeprecationList::class)]
#[Framework\Attributes\UsesClass(Report\Diff::class)]
#[Framework\Attributes\UsesClass(Report\ErroredTest::class)]
#[Framework\Attributes\UsesClass(Report\ErroredTestList::class)]
#[Framework\Attributes\UsesClass(Report\Expected::class)]
#[Framework\Attributes\UsesClass(Report\FailedTest::class)]
#[Framework\Attributes\UsesClass(Report\FailedTestList::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\IncompleteTest::class)]
#[Framework\Attributes\UsesClass(Report\IncompleteTestList::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\Notice::class)]
#[Framework\Attributes\UsesClass(Report\NoticeList::class)]
#[Framework\Attributes\UsesClass(Report\PhpunitDeprecation::class)]
#[Framework\Attributes\UsesClass(Report\PhpunitDeprecationList::class)]
#[Framework\Attributes\UsesClass(Report\PhpunitNotice::class)]
#[Framework\Attributes\UsesClass(Report\PhpunitNoticeList::class)]
#[Framework\Attributes\UsesClass(Report\PhpunitWarning::class)]
#[Framework\Attributes\UsesClass(Report\PhpunitWarningList::class)]
#[Framework\Attributes\UsesClass(Report\Report::class)]
#[Framework\Attributes\UsesClass(Report\Result::class)]
#[Framework\Attributes\UsesClass(Report\RiskyTest::class)]
#[Framework\Attributes\UsesClass(Report\RiskyTestList::class)]
#[Framework\Attributes\UsesClass(Report\ShellExitCode::class)]
#[Framework\Attributes\UsesClass(Report\SkippedTest::class)]
#[Framework\Attributes\UsesClass(Report\SkippedTestList::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifierList::class)]
#[Framework\Attributes\UsesClass(Report\Warning::class)]
#[Framework\Attributes\UsesClass(Report\WarningList::class)]
final class JsonReporterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testReportReturnsJsonWithResultSuccessWhenShellExitCodeIsSuccess(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::success(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'result' => Report\Result::success()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWithResultFailureWhenShellExitCodeIsNotSuccess(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::fromInt($faker->numberBetween(1, 100)),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasErroredTests(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::exception(),
            Report\ErroredTestList::create(...\array_map(static function () use ($faker): Report\ErroredTest {
                return Report\ErroredTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'errors' => \array_map(static function (Report\ErroredTest $erroredTest): array {
                        return [
                            'file' => $erroredTest->file()->toString(),
                            'line' => $erroredTest->line()->toInt(),
                            'message' => $erroredTest->message()->toString(),
                            'test' => $erroredTest->testIdentifier()->toString(),
                        ];
                    }, $report->erroredTestList()->toArray()),
                ],
                'result' => Report\Result::exception()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasFailedTestsWithComparisonFailure(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(...\array_map(static function () use ($faker): Report\FailedTest {
                return Report\FailedTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    Report\ComparisonFailure::create(
                        Report\Actual::fromString($faker->sentence()),
                        Report\Diff::fromString($faker->sentence()),
                        Report\Expected::fromString($faker->sentence()),
                    ),
                );
            }, \range(0, 2))),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'failures' => \array_map(static function (Report\FailedTest $failedTest): array {
                        $comparisonFailure = $failedTest->comparisonFailure();

                        self::assertInstanceOf(Report\ComparisonFailure::class, $comparisonFailure);

                        return [
                            'actual' => $comparisonFailure->actual()->toString(),
                            'diff' => $comparisonFailure->diff()->toString(),
                            'expected' => $comparisonFailure->expected()->toString(),
                            'file' => $failedTest->file()->toString(),
                            'line' => $failedTest->line()->toInt(),
                            'message' => $failedTest->message()->toString(),
                            'test' => $failedTest->testIdentifier()->toString(),
                        ];
                    }, $report->failedTestList()->toArray()),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasFailedTestsWithoutComparisonFailure(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(...\array_map(static function () use ($faker): Report\FailedTest {
                return Report\FailedTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    null,
                );
            }, \range(0, 2))),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'failures' => \array_map(static function (Report\FailedTest $failedTest): array {
                        return [
                            'file' => $failedTest->file()->toString(),
                            'line' => $failedTest->line()->toInt(),
                            'message' => $failedTest->message()->toString(),
                            'test' => $failedTest->testIdentifier()->toString(),
                        ];
                    }, $report->failedTestList()->toArray()),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasSkippedTests(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::success(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(...\array_map(static function () use ($faker): Report\SkippedTest {
                return Report\SkippedTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'skipped' => \array_map(static function (Report\SkippedTest $skippedTest): array {
                        return [
                            'file' => $skippedTest->file()->toString(),
                            'line' => $skippedTest->line()->toInt(),
                            'message' => $skippedTest->message()->toString(),
                            'test' => $skippedTest->testIdentifier()->toString(),
                        ];
                    }, $report->skippedTestList()->toArray()),
                ],
                'result' => Report\Result::success()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasIncompleteTests(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(...\array_map(static function () use ($faker): Report\IncompleteTest {
                return Report\IncompleteTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'incomplete' => \array_map(static function (Report\IncompleteTest $incompleteTest): array {
                        return [
                            'file' => $incompleteTest->file()->toString(),
                            'line' => $incompleteTest->line()->toInt(),
                            'message' => $incompleteTest->message()->toString(),
                            'test' => $incompleteTest->testIdentifier()->toString(),
                        ];
                    }, $report->incompleteTestList()->toArray()),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasDeprecations(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(...\array_map(static function () use ($faker): Report\Deprecation {
                return Report\Deprecation::create(
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                        return Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'deprecations' => \array_map(static function (Report\Deprecation $deprecation): array {
                        return [
                            'file' => $deprecation->file()->toString(),
                            'line' => $deprecation->line()->toInt(),
                            'message' => $deprecation->message()->toString(),
                            'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $deprecation->triggeredBy()->toArray()),
                        ];
                    }, $report->deprecationList()->toArray()),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasWarnings(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(...\array_map(static function () use ($faker): Report\Warning {
                return Report\Warning::create(
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                        return Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'warnings' => \array_map(static function (Report\Warning $warning): array {
                        return [
                            'file' => $warning->file()->toString(),
                            'line' => $warning->line()->toInt(),
                            'message' => $warning->message()->toString(),
                            'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $warning->triggeredBy()->toArray()),
                        ];
                    }, $report->warningList()->toArray()),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasNotices(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(...\array_map(static function () use ($faker): Report\Notice {
                return Report\Notice::create(
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                        return Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'notices' => \array_map(static function (Report\Notice $notice): array {
                        return [
                            'file' => $notice->file()->toString(),
                            'line' => $notice->line()->toInt(),
                            'message' => $notice->message()->toString(),
                            'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $notice->triggeredBy()->toArray()),
                        ];
                    }, $report->noticeList()->toArray()),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasRiskyTests(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(...\array_map(static function () use ($faker): Report\RiskyTest {
                return Report\RiskyTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'risky' => \array_map(static function (Report\RiskyTest $riskyTest): array {
                        return [
                            'file' => $riskyTest->file()->toString(),
                            'line' => $riskyTest->line()->toInt(),
                            'message' => $riskyTest->message()->toString(),
                            'test' => $riskyTest->testIdentifier()->toString(),
                        ];
                    }, $report->riskyTestList()->toArray()),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasPhpunitDeprecations(): void
    {
        $faker = self::faker();

        $phpunitDeprecationsTriggeredByTest = \array_map(static function () use ($faker): Report\PhpunitDeprecation {
            return Report\PhpunitDeprecation::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 1000)),
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $phpunitDeprecationsTriggeredByTestRunner = \array_map(static function () use ($faker): Report\PhpunitDeprecation {
            return Report\PhpunitDeprecation::create(
                null,
                null,
                null,
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(...\array_merge(
                $phpunitDeprecationsTriggeredByTest,
                $phpunitDeprecationsTriggeredByTestRunner,
            )),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'phpunitDeprecations' => \array_merge(
                        \array_map(static function (Report\PhpunitDeprecation $phpunitDeprecation): array {
                            $testIdentifier = $phpunitDeprecation->testIdentifier();
                            $file = $phpunitDeprecation->file();
                            $line = $phpunitDeprecation->line();

                            self::assertInstanceOf(Report\TestIdentifier::class, $testIdentifier);
                            self::assertInstanceOf(Report\File::class, $file);
                            self::assertInstanceOf(Report\Line::class, $line);

                            return [
                                'file' => $file->toString(),
                                'line' => $line->toInt(),
                                'message' => $phpunitDeprecation->message()->toString(),
                                'test' => $testIdentifier->toString(),
                            ];
                        }, $phpunitDeprecationsTriggeredByTest),
                        \array_map(static function (Report\PhpunitDeprecation $phpunitDeprecation): array {
                            return [
                                'message' => $phpunitDeprecation->message()->toString(),
                            ];
                        }, $phpunitDeprecationsTriggeredByTestRunner),
                    ),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasPhpunitNotices(): void
    {
        $faker = self::faker();

        $phpunitNoticesTriggeredByTest = \array_map(static function () use ($faker): Report\PhpunitNotice {
            return Report\PhpunitNotice::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 1000)),
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $phpunitNoticesTriggeredByTestRunner = \array_map(static function () use ($faker): Report\PhpunitNotice {
            return Report\PhpunitNotice::create(
                null,
                null,
                null,
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(...\array_merge(
                $phpunitNoticesTriggeredByTest,
                $phpunitNoticesTriggeredByTestRunner,
            )),
            Report\PhpunitWarningList::create(),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'phpunitNotices' => \array_merge(
                        \array_map(static function (Report\PhpunitNotice $phpunitNotice): array {
                            $testIdentifier = $phpunitNotice->testIdentifier();
                            $file = $phpunitNotice->file();
                            $line = $phpunitNotice->line();

                            self::assertInstanceOf(Report\TestIdentifier::class, $testIdentifier);
                            self::assertInstanceOf(Report\File::class, $file);
                            self::assertInstanceOf(Report\Line::class, $line);

                            return [
                                'file' => $file->toString(),
                                'line' => $line->toInt(),
                                'message' => $phpunitNotice->message()->toString(),
                                'test' => $testIdentifier->toString(),
                            ];
                        }, $phpunitNoticesTriggeredByTest),
                        \array_map(static function (Report\PhpunitNotice $phpunitNotice): array {
                            return [
                                'message' => $phpunitNotice->message()->toString(),
                            ];
                        }, $phpunitNoticesTriggeredByTestRunner),
                    ),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasPhpunitWarnings(): void
    {
        $faker = self::faker();

        $phpunitWarningsTriggeredByTest = \array_map(static function () use ($faker): Report\PhpunitWarning {
            return Report\PhpunitWarning::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 1000)),
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $phpunitWarningsTriggeredByTestRunner = \array_map(static function () use ($faker): Report\PhpunitWarning {
            return Report\PhpunitWarning::create(
                null,
                null,
                null,
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(),
            Report\FailedTestList::create(),
            Report\IncompleteTestList::create(),
            Report\SkippedTestList::create(),
            Report\RiskyTestList::create(),
            Report\DeprecationList::create(),
            Report\NoticeList::create(),
            Report\WarningList::create(),
            Report\PhpunitDeprecationList::create(),
            Report\PhpunitNoticeList::create(),
            Report\PhpunitWarningList::create(...\array_merge(
                $phpunitWarningsTriggeredByTest,
                $phpunitWarningsTriggeredByTestRunner,
            )),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'phpunitWarnings' => \array_merge(
                        \array_map(static function (Report\PhpunitWarning $phpunitWarning): array {
                            $testIdentifier = $phpunitWarning->testIdentifier();
                            $file = $phpunitWarning->file();
                            $line = $phpunitWarning->line();

                            self::assertInstanceOf(Report\TestIdentifier::class, $testIdentifier);
                            self::assertInstanceOf(Report\File::class, $file);
                            self::assertInstanceOf(Report\Line::class, $line);

                            return [
                                'file' => $file->toString(),
                                'line' => $line->toInt(),
                                'message' => $phpunitWarning->message()->toString(),
                                'test' => $testIdentifier->toString(),
                            ];
                        }, $phpunitWarningsTriggeredByTest),
                        \array_map(static function (Report\PhpunitWarning $phpunitWarning): array {
                            return [
                                'message' => $phpunitWarning->message()->toString(),
                            ];
                        }, $phpunitWarningsTriggeredByTestRunner),
                    ),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenReportHasAllCategories(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
            Report\ErroredTestList::create(...\array_map(static function () use ($faker): Report\ErroredTest {
                return Report\ErroredTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\FailedTestList::create(...\array_map(static function () use ($faker): Report\FailedTest {
                return Report\FailedTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    null,
                );
            }, \range(0, 2))),
            Report\IncompleteTestList::create(...\array_map(static function () use ($faker): Report\IncompleteTest {
                return Report\IncompleteTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\SkippedTestList::create(...\array_map(static function () use ($faker): Report\SkippedTest {
                return Report\SkippedTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\RiskyTestList::create(...\array_map(static function () use ($faker): Report\RiskyTest {
                return Report\RiskyTest::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\DeprecationList::create(...\array_map(static function () use ($faker): Report\Deprecation {
                return Report\Deprecation::create(
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                        return Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            Report\NoticeList::create(...\array_map(static function () use ($faker): Report\Notice {
                return Report\Notice::create(
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                        return Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            Report\WarningList::create(...\array_map(static function () use ($faker): Report\Warning {
                return Report\Warning::create(
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                    Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                        return Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            Report\PhpunitDeprecationList::create(...\array_map(static function () use ($faker): Report\PhpunitDeprecation {
                return Report\PhpunitDeprecation::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\PhpunitNoticeList::create(...\array_map(static function () use ($faker): Report\PhpunitNotice {
                return Report\PhpunitNotice::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\PhpunitWarningList::create(...\array_map(static function () use ($faker): Report\PhpunitWarning {
                return Report\PhpunitWarning::create(
                    Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'deprecations' => \array_map(static function (Report\Deprecation $deprecation): array {
                        return [
                            'file' => $deprecation->file()->toString(),
                            'line' => $deprecation->line()->toInt(),
                            'message' => $deprecation->message()->toString(),
                            'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $deprecation->triggeredBy()->toArray()),
                        ];
                    }, $report->deprecationList()->toArray()),
                    'errors' => \array_map(static function (Report\ErroredTest $erroredTest): array {
                        return [
                            'file' => $erroredTest->file()->toString(),
                            'line' => $erroredTest->line()->toInt(),
                            'message' => $erroredTest->message()->toString(),
                            'test' => $erroredTest->testIdentifier()->toString(),
                        ];
                    }, $report->erroredTestList()->toArray()),
                    'failures' => \array_map(static function (Report\FailedTest $failedTest): array {
                        return [
                            'file' => $failedTest->file()->toString(),
                            'line' => $failedTest->line()->toInt(),
                            'message' => $failedTest->message()->toString(),
                            'test' => $failedTest->testIdentifier()->toString(),
                        ];
                    }, $report->failedTestList()->toArray()),
                    'incomplete' => \array_map(static function (Report\IncompleteTest $incompleteTest): array {
                        return [
                            'file' => $incompleteTest->file()->toString(),
                            'line' => $incompleteTest->line()->toInt(),
                            'message' => $incompleteTest->message()->toString(),
                            'test' => $incompleteTest->testIdentifier()->toString(),
                        ];
                    }, $report->incompleteTestList()->toArray()),
                    'notices' => \array_map(static function (Report\Notice $notice): array {
                        return [
                            'file' => $notice->file()->toString(),
                            'line' => $notice->line()->toInt(),
                            'message' => $notice->message()->toString(),
                            'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $notice->triggeredBy()->toArray()),
                        ];
                    }, $report->noticeList()->toArray()),
                    'phpunitDeprecations' => \array_map(static function (Report\PhpunitDeprecation $phpunitDeprecation): array {
                        $testIdentifier = $phpunitDeprecation->testIdentifier();
                        $file = $phpunitDeprecation->file();
                        $line = $phpunitDeprecation->line();

                        self::assertInstanceOf(Report\TestIdentifier::class, $testIdentifier);
                        self::assertInstanceOf(Report\File::class, $file);
                        self::assertInstanceOf(Report\Line::class, $line);

                        return [
                            'file' => $file->toString(),
                            'line' => $line->toInt(),
                            'message' => $phpunitDeprecation->message()->toString(),
                            'test' => $testIdentifier->toString(),
                        ];
                    }, $report->phpunitDeprecationList()->toArray()),
                    'phpunitNotices' => \array_map(static function (Report\PhpunitNotice $phpunitNotice): array {
                        $testIdentifier = $phpunitNotice->testIdentifier();
                        $file = $phpunitNotice->file();
                        $line = $phpunitNotice->line();

                        self::assertInstanceOf(Report\TestIdentifier::class, $testIdentifier);
                        self::assertInstanceOf(Report\File::class, $file);
                        self::assertInstanceOf(Report\Line::class, $line);

                        return [
                            'file' => $file->toString(),
                            'line' => $line->toInt(),
                            'message' => $phpunitNotice->message()->toString(),
                            'test' => $testIdentifier->toString(),
                        ];
                    }, $report->phpunitNoticeList()->toArray()),
                    'phpunitWarnings' => \array_map(static function (Report\PhpunitWarning $phpunitWarning): array {
                        $testIdentifier = $phpunitWarning->testIdentifier();
                        $file = $phpunitWarning->file();
                        $line = $phpunitWarning->line();

                        self::assertInstanceOf(Report\TestIdentifier::class, $testIdentifier);
                        self::assertInstanceOf(Report\File::class, $file);
                        self::assertInstanceOf(Report\Line::class, $line);

                        return [
                            'file' => $file->toString(),
                            'line' => $line->toInt(),
                            'message' => $phpunitWarning->message()->toString(),
                            'test' => $testIdentifier->toString(),
                        ];
                    }, $report->phpunitWarningList()->toArray()),
                    'risky' => \array_map(static function (Report\RiskyTest $riskyTest): array {
                        return [
                            'file' => $riskyTest->file()->toString(),
                            'line' => $riskyTest->line()->toInt(),
                            'message' => $riskyTest->message()->toString(),
                            'test' => $riskyTest->testIdentifier()->toString(),
                        ];
                    }, $report->riskyTestList()->toArray()),
                    'skipped' => \array_map(static function (Report\SkippedTest $skippedTest): array {
                        return [
                            'file' => $skippedTest->file()->toString(),
                            'line' => $skippedTest->line()->toInt(),
                            'message' => $skippedTest->message()->toString(),
                            'test' => $skippedTest->testIdentifier()->toString(),
                        ];
                    }, $report->skippedTestList()->toArray()),
                    'warnings' => \array_map(static function (Report\Warning $warning): array {
                        return [
                            'file' => $warning->file()->toString(),
                            'line' => $warning->line()->toInt(),
                            'message' => $warning->message()->toString(),
                            'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $warning->triggeredBy()->toArray()),
                        ];
                    }, $report->warningList()->toArray()),
                ],
                'result' => Report\Result::failure()->toString(),
                'summary' => self::summary($report),
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    /**
     * @return array{
     *     assertions: int,
     *     deprecations: int,
     *     errors: int,
     *     failures: int,
     *     incomplete: int,
     *     notices: int,
     *     phpunitDeprecations: int,
     *     phpunitNotices: int,
     *     phpunitWarnings: int,
     *     risky: int,
     *     skipped: int,
     *     tests: int,
     *     warnings: int
     * }
     */
    private static function summary(Report\Report $report): array
    {
        return [
            'assertions' => $report->totalAssertionCount()->toInt(),
            'deprecations' => $report->deprecationList()->count()->toInt(),
            'errors' => $report->erroredTestList()->count()->toInt(),
            'failures' => $report->failedTestList()->count()->toInt(),
            'incomplete' => $report->incompleteTestList()->count()->toInt(),
            'notices' => $report->noticeList()->count()->toInt(),
            'phpunitDeprecations' => $report->phpunitDeprecationList()->count()->toInt(),
            'phpunitNotices' => $report->phpunitNoticeList()->count()->toInt(),
            'phpunitWarnings' => $report->phpunitWarningList()->count()->toInt(),
            'risky' => $report->riskyTestList()->count()->toInt(),
            'skipped' => $report->skippedTestList()->count()->toInt(),
            'tests' => $report->totalTestCount()->toInt(),
            'warnings' => $report->warningList()->count()->toInt(),
        ];
    }
}
