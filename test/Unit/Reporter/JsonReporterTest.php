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

use Ergebnis\PHPUnit\AgentReporter;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(AgentReporter\Reporter\JsonReporter::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Actual::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ComparisonFailure::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Count::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Deprecation::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\DeprecationList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Diff::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ErroredTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ErroredTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Expected::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\FailedTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\FailedTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\IncompleteTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\IncompleteTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Notice::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\NoticeList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitDeprecation::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitDeprecationList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitNotice::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitNoticeList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitWarning::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitWarningList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Report::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Result::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\RiskyTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\RiskyTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ShellExitCode::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\SkippedTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\SkippedTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifierList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Warning::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\WarningList::class)]
final class JsonReporterTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testReportReturnsJsonWithResultSuccessWhenShellExitCodeIsSuccess(): void
    {
        $faker = self::faker();

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::success(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'result' => AgentReporter\Report\Result::success()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::exception(),
            AgentReporter\Report\ErroredTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\ErroredTest {
                return AgentReporter\Report\ErroredTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'errors' => \array_map(static function (AgentReporter\Report\ErroredTest $erroredTest): array {
                        return [
                            'file' => $erroredTest->file()->toString(),
                            'line' => $erroredTest->line()->toInt(),
                            'message' => $erroredTest->message()->toString(),
                            'test' => $erroredTest->testIdentifier()->toString(),
                        ];
                    }, $report->erroredTestList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::exception()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\FailedTest {
                return AgentReporter\Report\FailedTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    AgentReporter\Report\ComparisonFailure::create(
                        AgentReporter\Report\Actual::fromString($faker->sentence()),
                        AgentReporter\Report\Diff::fromString($faker->sentence()),
                        AgentReporter\Report\Expected::fromString($faker->sentence()),
                    ),
                );
            }, \range(0, 2))),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'failures' => \array_map(static function (AgentReporter\Report\FailedTest $failedTest): array {
                        $comparisonFailure = $failedTest->comparisonFailure();

                        self::assertInstanceOf(AgentReporter\Report\ComparisonFailure::class, $comparisonFailure);

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
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\FailedTest {
                return AgentReporter\Report\FailedTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    null,
                );
            }, \range(0, 2))),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'failures' => \array_map(static function (AgentReporter\Report\FailedTest $failedTest): array {
                        return [
                            'file' => $failedTest->file()->toString(),
                            'line' => $failedTest->line()->toInt(),
                            'message' => $failedTest->message()->toString(),
                            'test' => $failedTest->testIdentifier()->toString(),
                        ];
                    }, $report->failedTestList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::success(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\SkippedTest {
                return AgentReporter\Report\SkippedTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'skipped' => \array_map(static function (AgentReporter\Report\SkippedTest $skippedTest): array {
                        return [
                            'file' => $skippedTest->file()->toString(),
                            'line' => $skippedTest->line()->toInt(),
                            'message' => $skippedTest->message()->toString(),
                            'test' => $skippedTest->testIdentifier()->toString(),
                        ];
                    }, $report->skippedTestList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::success()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\IncompleteTest {
                return AgentReporter\Report\IncompleteTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'incomplete' => \array_map(static function (AgentReporter\Report\IncompleteTest $incompleteTest): array {
                        return [
                            'file' => $incompleteTest->file()->toString(),
                            'line' => $incompleteTest->line()->toInt(),
                            'message' => $incompleteTest->message()->toString(),
                            'test' => $incompleteTest->testIdentifier()->toString(),
                        ];
                    }, $report->incompleteTestList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Deprecation {
                return AgentReporter\Report\Deprecation::create(
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                        return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'deprecations' => \array_map(static function (AgentReporter\Report\Deprecation $deprecation): array {
                        return [
                            'file' => $deprecation->file()->toString(),
                            'line' => $deprecation->line()->toInt(),
                            'message' => $deprecation->message()->toString(),
                            'triggeredBy' => \array_map(static function (AgentReporter\Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $deprecation->triggeredBy()->toArray()),
                        ];
                    }, $report->deprecationList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Warning {
                return AgentReporter\Report\Warning::create(
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                        return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'warnings' => \array_map(static function (AgentReporter\Report\Warning $warning): array {
                        return [
                            'file' => $warning->file()->toString(),
                            'line' => $warning->line()->toInt(),
                            'message' => $warning->message()->toString(),
                            'triggeredBy' => \array_map(static function (AgentReporter\Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $warning->triggeredBy()->toArray()),
                        ];
                    }, $report->warningList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Notice {
                return AgentReporter\Report\Notice::create(
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                        return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'notices' => \array_map(static function (AgentReporter\Report\Notice $notice): array {
                        return [
                            'file' => $notice->file()->toString(),
                            'line' => $notice->line()->toInt(),
                            'message' => $notice->message()->toString(),
                            'triggeredBy' => \array_map(static function (AgentReporter\Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $notice->triggeredBy()->toArray()),
                        ];
                    }, $report->noticeList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\RiskyTest {
                return AgentReporter\Report\RiskyTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'risky' => \array_map(static function (AgentReporter\Report\RiskyTest $riskyTest): array {
                        return [
                            'file' => $riskyTest->file()->toString(),
                            'line' => $riskyTest->line()->toInt(),
                            'message' => $riskyTest->message()->toString(),
                            'test' => $riskyTest->testIdentifier()->toString(),
                        ];
                    }, $report->riskyTestList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $phpunitDeprecationsTriggeredByTest = \array_map(static function () use ($faker): AgentReporter\Report\PhpunitDeprecation {
            return AgentReporter\Report\PhpunitDeprecation::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $phpunitDeprecationsTriggeredByTestRunner = \array_map(static function () use ($faker): AgentReporter\Report\PhpunitDeprecation {
            return AgentReporter\Report\PhpunitDeprecation::create(
                null,
                null,
                null,
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(...\array_merge(
                $phpunitDeprecationsTriggeredByTest,
                $phpunitDeprecationsTriggeredByTestRunner,
            )),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'phpunitDeprecations' => \array_merge(
                        \array_map(static function (AgentReporter\Report\PhpunitDeprecation $phpunitDeprecation): array {
                            $testIdentifier = $phpunitDeprecation->testIdentifier();
                            $file = $phpunitDeprecation->file();
                            $line = $phpunitDeprecation->line();

                            self::assertInstanceOf(AgentReporter\Report\TestIdentifier::class, $testIdentifier);
                            self::assertInstanceOf(AgentReporter\Report\File::class, $file);
                            self::assertInstanceOf(AgentReporter\Report\Line::class, $line);

                            return [
                                'file' => $file->toString(),
                                'line' => $line->toInt(),
                                'message' => $phpunitDeprecation->message()->toString(),
                                'test' => $testIdentifier->toString(),
                            ];
                        }, $phpunitDeprecationsTriggeredByTest),
                        \array_map(static function (AgentReporter\Report\PhpunitDeprecation $phpunitDeprecation): array {
                            return [
                                'message' => $phpunitDeprecation->message()->toString(),
                            ];
                        }, $phpunitDeprecationsTriggeredByTestRunner),
                    ),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $phpunitNoticesTriggeredByTest = \array_map(static function () use ($faker): AgentReporter\Report\PhpunitNotice {
            return AgentReporter\Report\PhpunitNotice::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $phpunitNoticesTriggeredByTestRunner = \array_map(static function () use ($faker): AgentReporter\Report\PhpunitNotice {
            return AgentReporter\Report\PhpunitNotice::create(
                null,
                null,
                null,
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(...\array_merge(
                $phpunitNoticesTriggeredByTest,
                $phpunitNoticesTriggeredByTestRunner,
            )),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'phpunitNotices' => \array_merge(
                        \array_map(static function (AgentReporter\Report\PhpunitNotice $phpunitNotice): array {
                            $testIdentifier = $phpunitNotice->testIdentifier();
                            $file = $phpunitNotice->file();
                            $line = $phpunitNotice->line();

                            self::assertInstanceOf(AgentReporter\Report\TestIdentifier::class, $testIdentifier);
                            self::assertInstanceOf(AgentReporter\Report\File::class, $file);
                            self::assertInstanceOf(AgentReporter\Report\Line::class, $line);

                            return [
                                'file' => $file->toString(),
                                'line' => $line->toInt(),
                                'message' => $phpunitNotice->message()->toString(),
                                'test' => $testIdentifier->toString(),
                            ];
                        }, $phpunitNoticesTriggeredByTest),
                        \array_map(static function (AgentReporter\Report\PhpunitNotice $phpunitNotice): array {
                            return [
                                'message' => $phpunitNotice->message()->toString(),
                            ];
                        }, $phpunitNoticesTriggeredByTestRunner),
                    ),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $phpunitWarningsTriggeredByTest = \array_map(static function () use ($faker): AgentReporter\Report\PhpunitWarning {
            return AgentReporter\Report\PhpunitWarning::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $phpunitWarningsTriggeredByTestRunner = \array_map(static function () use ($faker): AgentReporter\Report\PhpunitWarning {
            return AgentReporter\Report\PhpunitWarning::create(
                null,
                null,
                null,
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2));

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(...\array_merge(
                $phpunitWarningsTriggeredByTest,
                $phpunitWarningsTriggeredByTestRunner,
            )),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'phpunitWarnings' => \array_merge(
                        \array_map(static function (AgentReporter\Report\PhpunitWarning $phpunitWarning): array {
                            $testIdentifier = $phpunitWarning->testIdentifier();
                            $file = $phpunitWarning->file();
                            $line = $phpunitWarning->line();

                            self::assertInstanceOf(AgentReporter\Report\TestIdentifier::class, $testIdentifier);
                            self::assertInstanceOf(AgentReporter\Report\File::class, $file);
                            self::assertInstanceOf(AgentReporter\Report\Line::class, $line);

                            return [
                                'file' => $file->toString(),
                                'line' => $line->toInt(),
                                'message' => $phpunitWarning->message()->toString(),
                                'test' => $testIdentifier->toString(),
                            ];
                        }, $phpunitWarningsTriggeredByTest),
                        \array_map(static function (AgentReporter\Report\PhpunitWarning $phpunitWarning): array {
                            return [
                                'message' => $phpunitWarning->message()->toString(),
                            ];
                        }, $phpunitWarningsTriggeredByTestRunner),
                    ),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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

        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\ErroredTest {
                return AgentReporter\Report\ErroredTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\FailedTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\FailedTest {
                return AgentReporter\Report\FailedTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    null,
                );
            }, \range(0, 2))),
            AgentReporter\Report\IncompleteTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\IncompleteTest {
                return AgentReporter\Report\IncompleteTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\SkippedTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\SkippedTest {
                return AgentReporter\Report\SkippedTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\RiskyTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\RiskyTest {
                return AgentReporter\Report\RiskyTest::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\DeprecationList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Deprecation {
                return AgentReporter\Report\Deprecation::create(
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                        return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            AgentReporter\Report\NoticeList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Notice {
                return AgentReporter\Report\Notice::create(
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                        return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            AgentReporter\Report\WarningList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Warning {
                return AgentReporter\Report\Warning::create(
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                    AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                        return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                            '%s::%s',
                            $faker->word(),
                            $faker->word(),
                        ));
                    }, \range(0, 2))),
                );
            }, \range(0, 2))),
            AgentReporter\Report\PhpunitDeprecationList::create(...\array_map(static function () use ($faker): AgentReporter\Report\PhpunitDeprecation {
                return AgentReporter\Report\PhpunitDeprecation::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\PhpunitNoticeList::create(...\array_map(static function () use ($faker): AgentReporter\Report\PhpunitNotice {
                return AgentReporter\Report\PhpunitNotice::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\PhpunitWarningList::create(...\array_map(static function () use ($faker): AgentReporter\Report\PhpunitWarning {
                return AgentReporter\Report\PhpunitWarning::create(
                    AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\File::fromString(\sprintf(
                        '%s/%s.php',
                        $faker->word(),
                        $faker->word(),
                    )),
                    AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 1000)),
                    AgentReporter\Report\Message::fromString($faker->sentence()),
                );
            }, \range(0, 2))),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
            AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100)),
        );

        $reporter = new AgentReporter\Reporter\JsonReporter();

        $expected = \json_encode(
            [
                'details' => [
                    'deprecations' => \array_map(static function (AgentReporter\Report\Deprecation $deprecation): array {
                        return [
                            'file' => $deprecation->file()->toString(),
                            'line' => $deprecation->line()->toInt(),
                            'message' => $deprecation->message()->toString(),
                            'triggeredBy' => \array_map(static function (AgentReporter\Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $deprecation->triggeredBy()->toArray()),
                        ];
                    }, $report->deprecationList()->toArray()),
                    'errors' => \array_map(static function (AgentReporter\Report\ErroredTest $erroredTest): array {
                        return [
                            'file' => $erroredTest->file()->toString(),
                            'line' => $erroredTest->line()->toInt(),
                            'message' => $erroredTest->message()->toString(),
                            'test' => $erroredTest->testIdentifier()->toString(),
                        ];
                    }, $report->erroredTestList()->toArray()),
                    'failures' => \array_map(static function (AgentReporter\Report\FailedTest $failedTest): array {
                        return [
                            'file' => $failedTest->file()->toString(),
                            'line' => $failedTest->line()->toInt(),
                            'message' => $failedTest->message()->toString(),
                            'test' => $failedTest->testIdentifier()->toString(),
                        ];
                    }, $report->failedTestList()->toArray()),
                    'incomplete' => \array_map(static function (AgentReporter\Report\IncompleteTest $incompleteTest): array {
                        return [
                            'file' => $incompleteTest->file()->toString(),
                            'line' => $incompleteTest->line()->toInt(),
                            'message' => $incompleteTest->message()->toString(),
                            'test' => $incompleteTest->testIdentifier()->toString(),
                        ];
                    }, $report->incompleteTestList()->toArray()),
                    'notices' => \array_map(static function (AgentReporter\Report\Notice $notice): array {
                        return [
                            'file' => $notice->file()->toString(),
                            'line' => $notice->line()->toInt(),
                            'message' => $notice->message()->toString(),
                            'triggeredBy' => \array_map(static function (AgentReporter\Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $notice->triggeredBy()->toArray()),
                        ];
                    }, $report->noticeList()->toArray()),
                    'phpunitDeprecations' => \array_map(static function (AgentReporter\Report\PhpunitDeprecation $phpunitDeprecation): array {
                        $testIdentifier = $phpunitDeprecation->testIdentifier();
                        $file = $phpunitDeprecation->file();
                        $line = $phpunitDeprecation->line();

                        self::assertInstanceOf(AgentReporter\Report\TestIdentifier::class, $testIdentifier);
                        self::assertInstanceOf(AgentReporter\Report\File::class, $file);
                        self::assertInstanceOf(AgentReporter\Report\Line::class, $line);

                        return [
                            'file' => $file->toString(),
                            'line' => $line->toInt(),
                            'message' => $phpunitDeprecation->message()->toString(),
                            'test' => $testIdentifier->toString(),
                        ];
                    }, $report->phpunitDeprecationList()->toArray()),
                    'phpunitNotices' => \array_map(static function (AgentReporter\Report\PhpunitNotice $phpunitNotice): array {
                        $testIdentifier = $phpunitNotice->testIdentifier();
                        $file = $phpunitNotice->file();
                        $line = $phpunitNotice->line();

                        self::assertInstanceOf(AgentReporter\Report\TestIdentifier::class, $testIdentifier);
                        self::assertInstanceOf(AgentReporter\Report\File::class, $file);
                        self::assertInstanceOf(AgentReporter\Report\Line::class, $line);

                        return [
                            'file' => $file->toString(),
                            'line' => $line->toInt(),
                            'message' => $phpunitNotice->message()->toString(),
                            'test' => $testIdentifier->toString(),
                        ];
                    }, $report->phpunitNoticeList()->toArray()),
                    'phpunitWarnings' => \array_map(static function (AgentReporter\Report\PhpunitWarning $phpunitWarning): array {
                        $testIdentifier = $phpunitWarning->testIdentifier();
                        $file = $phpunitWarning->file();
                        $line = $phpunitWarning->line();

                        self::assertInstanceOf(AgentReporter\Report\TestIdentifier::class, $testIdentifier);
                        self::assertInstanceOf(AgentReporter\Report\File::class, $file);
                        self::assertInstanceOf(AgentReporter\Report\Line::class, $line);

                        return [
                            'file' => $file->toString(),
                            'line' => $line->toInt(),
                            'message' => $phpunitWarning->message()->toString(),
                            'test' => $testIdentifier->toString(),
                        ];
                    }, $report->phpunitWarningList()->toArray()),
                    'risky' => \array_map(static function (AgentReporter\Report\RiskyTest $riskyTest): array {
                        return [
                            'file' => $riskyTest->file()->toString(),
                            'line' => $riskyTest->line()->toInt(),
                            'message' => $riskyTest->message()->toString(),
                            'test' => $riskyTest->testIdentifier()->toString(),
                        ];
                    }, $report->riskyTestList()->toArray()),
                    'skipped' => \array_map(static function (AgentReporter\Report\SkippedTest $skippedTest): array {
                        return [
                            'file' => $skippedTest->file()->toString(),
                            'line' => $skippedTest->line()->toInt(),
                            'message' => $skippedTest->message()->toString(),
                            'test' => $skippedTest->testIdentifier()->toString(),
                        ];
                    }, $report->skippedTestList()->toArray()),
                    'warnings' => \array_map(static function (AgentReporter\Report\Warning $warning): array {
                        return [
                            'file' => $warning->file()->toString(),
                            'line' => $warning->line()->toInt(),
                            'message' => $warning->message()->toString(),
                            'triggeredBy' => \array_map(static function (AgentReporter\Report\TestIdentifier $testIdentifier): string {
                                return $testIdentifier->toString();
                            }, $warning->triggeredBy()->toArray()),
                        ];
                    }, $report->warningList()->toArray()),
                ],
                'result' => AgentReporter\Report\Result::failure()->toString(),
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
    private static function summary(AgentReporter\Report\Report $report): array
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
