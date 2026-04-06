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
use PHPUnit\TextUI;

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

    public function testReportReturnsJsonWithResultPassedWhenShellExitCodeIsSuccess(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithDefaults();

        $reporter = new Reporter\JsonReporter($configuration);

        $expected = \json_encode(
            [
                'result' => Report\Result::success()->toString(),
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWithResultFailedWhenShellExitCodeIsNotSucccess(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithDefaults();

        $reporter = new Reporter\JsonReporter($configuration);

        $expected = \json_encode(
            [
                'result' => Report\Result::failure()->toString(),
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenRecordHasErroredTests(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithDefaults();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenRecordHasFailedTestsWithComparisonFailure(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithDefaults();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportReturnsJsonWhenRecordHasFailedTestsWithoutComparisonFailure(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithDefaults();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportDoesNotIncludeSkippedWhenFailOnSkippedIsNotActive(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithDefaults();

        $reporter = new Reporter\JsonReporter($configuration);

        $expected = \json_encode(
            [
                'result' => Report\Result::success()->toString(),
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportIncludesSkippedWhenFailOnSkippedIsActive(): void
    {
        $faker = self::faker();

        $report = Report\Report::create(
            Report\ShellExitCode::failure(),
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithFailOnSkipped();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'result' => Report\Result::failure()->toString(),
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'skipped' => $report->skippedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportIncludesIncompleteWhenFailOnIncompleteIsActive(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithFailOnIncomplete();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'incomplete' => $report->incompleteTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportIncludesDeprecationsWhenFailOnDeprecationIsActive(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithFailOnDeprecation();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'deprecations' => $report->deprecationList()->count()->toInt(),
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportIncludesWarningsWhenFailOnWarningIsActive(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithFailOnWarning();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportIncludesNoticesWhenFailOnNoticeIsActive(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithFailOnNotice();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'notices' => $report->noticeList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportIncludesRiskyWhenFailOnRiskyIsActive(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithFailOnRisky();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'risky' => $report->riskyTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    public function testReportIncludesAllCategoriesWhenFailOnAllIssuesIsActive(): void
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
            Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $configuration = self::createConfigurationWithFailOnAllIssues();

        $reporter = new Reporter\JsonReporter($configuration);

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
                'summary' => [
                    'deprecations' => $report->deprecationList()->count()->toInt(),
                    'errors' => $report->erroredTestList()->count()->toInt(),
                    'failures' => $report->failedTestList()->count()->toInt(),
                    'incomplete' => $report->incompleteTestList()->count()->toInt(),
                    'notices' => $report->noticeList()->count()->toInt(),
                    'risky' => $report->riskyTestList()->count()->toInt(),
                    'skipped' => $report->skippedTestList()->count()->toInt(),
                    'tests' => $report->totalTestCount()->toInt(),
                    'warnings' => $report->warningList()->count()->toInt(),
                ],
            ],
            \JSON_THROW_ON_ERROR,
        );

        $json = $reporter->report($report);

        self::assertJsonStringEqualsJsonString($expected, $json);
        self::assertJsonSatisfiesAgentReportSchema($json);
    }

    private static function createConfigurationWithDefaults(): TextUI\Configuration\Configuration
    {
        $builder = new TextUI\Configuration\Builder();

        return $builder->build([
            self::configurationOption(),
        ]);
    }

    private static function createConfigurationWithFailOnAllIssues(): TextUI\Configuration\Configuration
    {
        $builder = new TextUI\Configuration\Builder();

        return $builder->build([
            self::configurationOption(),
            '--fail-on-all-issues',
        ]);
    }

    private static function createConfigurationWithFailOnDeprecation(): TextUI\Configuration\Configuration
    {
        $builder = new TextUI\Configuration\Builder();

        return $builder->build([
            self::configurationOption(),
            '--fail-on-deprecation',
        ]);
    }

    private static function createConfigurationWithFailOnIncomplete(): TextUI\Configuration\Configuration
    {
        $builder = new TextUI\Configuration\Builder();

        return $builder->build([
            self::configurationOption(),
            '--fail-on-incomplete',
        ]);
    }

    private static function createConfigurationWithFailOnNotice(): TextUI\Configuration\Configuration
    {
        $builder = new TextUI\Configuration\Builder();

        return $builder->build([
            self::configurationOption(),
            '--fail-on-notice',
        ]);
    }

    private static function createConfigurationWithFailOnRisky(): TextUI\Configuration\Configuration
    {
        $builder = new TextUI\Configuration\Builder();

        return $builder->build([
            self::configurationOption(),
            '--fail-on-risky',
        ]);
    }

    private static function createConfigurationWithFailOnSkipped(): TextUI\Configuration\Configuration
    {
        $builder = new TextUI\Configuration\Builder();

        return $builder->build([
            self::configurationOption(),
            '--fail-on-skipped',
        ]);
    }

    private static function createConfigurationWithFailOnWarning(): TextUI\Configuration\Configuration
    {
        $builder = new TextUI\Configuration\Builder();

        return $builder->build([
            self::configurationOption(),
            '--fail-on-warning',
        ]);
    }

    private static function configurationOption(): string
    {
        return \sprintf(
            '--configuration=%s',
            \realpath(__DIR__ . '/../phpunit.xml'),
        );
    }
}
