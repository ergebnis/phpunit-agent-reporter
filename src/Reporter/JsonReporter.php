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

use Ergebnis\PHPUnit\AgentReporter\Report;
use PHPUnit\TextUI;

/**
 * @internal
 */
final class JsonReporter implements Reporter
{
    private readonly TextUI\Configuration\Configuration $configuration;

    public function __construct(TextUI\Configuration\Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function report(Report\Report $report): string
    {
        $zero = Report\Count::zero();

        $summary = [
            'errors' => $report->erroredTestList()->count()->toInt(),
            'failures' => $report->failedTestList()->count()->toInt(),
            'tests' => $report->totalTestCount()->toInt(),
        ];

        if ($this->shouldIncludeDeprecations()) {
            $summary['deprecations'] = $report->deprecationList()->count()->toInt();
        }

        if ($this->shouldIncludeIncomplete()) {
            $summary['incomplete'] = $report->incompleteTestList()->count()->toInt();
        }

        if ($this->shouldIncludeNotices()) {
            $summary['notices'] = $report->noticeList()->count()->toInt();
        }

        if ($this->shouldIncludeRisky()) {
            $summary['risky'] = $report->riskyTestList()->count()->toInt();
        }

        if ($this->shouldIncludeSkipped()) {
            $summary['skipped'] = $report->skippedTestList()->count()->toInt();
        }

        if ($this->shouldIncludeWarnings()) {
            $summary['warnings'] = $report->warningList()->count()->toInt();
        }

        \ksort($summary);

        $details = [];

        if ($report->erroredTestList()->count()->isGreaterThan($zero)) {
            $details['errors'] = \array_map(static function (Report\ErroredTest $erroredTest): array {
                return [
                    'file' => $erroredTest->file()->toString(),
                    'line' => $erroredTest->line()->toInt(),
                    'message' => $erroredTest->message()->toString(),
                    'test' => $erroredTest->testIdentifier()->toString(),
                ];
            }, $report->erroredTestList()->toArray());
        }

        if ($report->failedTestList()->count()->isGreaterThan($zero)) {
            $details['failures'] = \array_map(static function (Report\FailedTest $failedTest): array {
                $item = [
                    'file' => $failedTest->file()->toString(),
                    'line' => $failedTest->line()->toInt(),
                    'message' => $failedTest->message()->toString(),
                    'test' => $failedTest->testIdentifier()->toString(),
                ];

                if ($failedTest->comparisonFailure() instanceof Report\ComparisonFailure) {
                    $item['actual'] = $failedTest->comparisonFailure()->actual()->toString();
                    $item['diff'] = $failedTest->comparisonFailure()->diff()->toString();
                    $item['expected'] = $failedTest->comparisonFailure()->expected()->toString();
                }

                \ksort($item);

                return $item;
            }, $report->failedTestList()->toArray());
        }

        if (
            $this->shouldIncludeDeprecations()
            && $report->deprecationList()->count()->isGreaterThan($zero)
        ) {
            $details['deprecations'] = $this->formatDeprecationList($report->deprecationList());
        }

        if (
            $this->shouldIncludeIncomplete()
            && $report->incompleteTestList()->count()->isGreaterThan($zero)
        ) {
            $details['incomplete'] = \array_map(static function (Report\IncompleteTest $incompleteTest): array {
                return [
                    'file' => $incompleteTest->file()->toString(),
                    'line' => $incompleteTest->line()->toInt(),
                    'message' => $incompleteTest->message()->toString(),
                    'test' => $incompleteTest->testIdentifier()->toString(),
                ];
            }, $report->incompleteTestList()->toArray());
        }

        if (
            $this->shouldIncludeNotices()
            && $report->noticeList()->count()->isGreaterThan($zero)
        ) {
            $details['notices'] = $this->formatNoticeList($report->noticeList());
        }

        if (
            $this->shouldIncludeRisky()
            && $report->riskyTestList()->count()->isGreaterThan($zero)
        ) {
            $details['risky'] = \array_map(static function (Report\RiskyTest $riskyTest): array {
                return [
                    'file' => $riskyTest->file()->toString(),
                    'line' => $riskyTest->line()->toInt(),
                    'message' => $riskyTest->message()->toString(),
                    'test' => $riskyTest->testIdentifier()->toString(),
                ];
            }, $report->riskyTestList()->toArray());
        }

        if (
            $this->shouldIncludeSkipped()
            && $report->skippedTestList()->count()->isGreaterThan($zero)
        ) {
            $details['skipped'] = \array_map(static function (Report\SkippedTest $skippedTest): array {
                return [
                    'file' => $skippedTest->file()->toString(),
                    'line' => $skippedTest->line()->toInt(),
                    'message' => $skippedTest->message()->toString(),
                    'test' => $skippedTest->testIdentifier()->toString(),
                ];
            }, $report->skippedTestList()->toArray());
        }

        if (
            $this->shouldIncludeWarnings()
            && $report->warningList()->count()->isGreaterThan($zero)
        ) {
            $details['warnings'] = $this->formatWarningList($report->warningList());
        }

        $data = [
            'result' => $report->result()->toString(),
            'summary' => $summary,
        ];

        if ([] !== $details) {
            $data['details'] = $details;
        }

        return \json_encode(
            $data,
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR,
        );
    }

    private function shouldIncludeDeprecations(): bool
    {
        $failOnDeprecation = false;
        $failOnPhpunitDeprecation = false;

        if ($this->configuration->failOnAllIssues()) {
            $failOnDeprecation = true;
            $failOnPhpunitDeprecation = true;
        }

        if ($this->configuration->failOnDeprecation()) {
            $failOnDeprecation = true;
        }

        if ($this->configuration->doNotFailOnDeprecation()) {
            $failOnDeprecation = false;
        }

        if ($this->configuration->failOnPhpunitDeprecation()) {
            $failOnPhpunitDeprecation = true;
        }

        if ($this->configuration->doNotFailOnPhpunitDeprecation()) {
            $failOnPhpunitDeprecation = false;
        }

        return $failOnDeprecation || $failOnPhpunitDeprecation;
    }

    private function shouldIncludeIncomplete(): bool
    {
        $failOnIncomplete = false;

        if ($this->configuration->failOnAllIssues()) {
            $failOnIncomplete = true;
        }

        if ($this->configuration->failOnIncomplete()) {
            $failOnIncomplete = true;
        }

        if ($this->configuration->doNotFailOnIncomplete()) {
            $failOnIncomplete = false;
        }

        return $failOnIncomplete;
    }

    private function shouldIncludeNotices(): bool
    {
        $failOnNotice = false;

        if ($this->configuration->failOnAllIssues()) {
            $failOnNotice = true;
        }

        if ($this->configuration->failOnNotice()) {
            $failOnNotice = true;
        }

        if ($this->configuration->doNotFailOnNotice()) {
            $failOnNotice = false;
        }

        return $failOnNotice;
    }

    private function shouldIncludeRisky(): bool
    {
        $failOnRisky = false;

        if ($this->configuration->failOnAllIssues()) {
            $failOnRisky = true;
        }

        if ($this->configuration->failOnRisky()) {
            $failOnRisky = true;
        }

        if ($this->configuration->doNotFailOnRisky()) {
            $failOnRisky = false;
        }

        return $failOnRisky;
    }

    private function shouldIncludeSkipped(): bool
    {
        $failOnSkipped = false;

        if ($this->configuration->failOnAllIssues()) {
            $failOnSkipped = true;
        }

        if ($this->configuration->failOnSkipped()) {
            $failOnSkipped = true;
        }

        if ($this->configuration->doNotFailOnSkipped()) {
            $failOnSkipped = false;
        }

        return $failOnSkipped;
    }

    private function shouldIncludeWarnings(): bool
    {
        $failOnWarning = false;
        $failOnPhpunitWarning = false;

        if ($this->configuration->failOnAllIssues()) {
            $failOnWarning = true;
            $failOnPhpunitWarning = true;
        }

        if ($this->configuration->failOnWarning()) {
            $failOnWarning = true;
        }

        if ($this->configuration->doNotFailOnWarning()) {
            $failOnWarning = false;
        }

        if ($this->configuration->failOnPhpunitWarning()) {
            $failOnPhpunitWarning = true;
        }

        if ($this->configuration->doNotFailOnPhpunitWarning()) {
            $failOnPhpunitWarning = false;
        }

        return $failOnWarning || $failOnPhpunitWarning;
    }

    /**
     * @return list<array{file: string, line: int, message: string, triggeredBy: list<string>}>
     */
    private function formatDeprecationList(Report\DeprecationList $deprecationList): array
    {
        return \array_map(static function (Report\Deprecation $deprecation): array {
            return [
                'file' => $deprecation->file()->toString(),
                'line' => $deprecation->line()->toInt(),
                'message' => $deprecation->message()->toString(),
                'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                    return $testIdentifier->toString();
                }, $deprecation->triggeredBy()->toArray()),
            ];
        }, $deprecationList->toArray());
    }

    /**
     * @return list<array{file: string, line: int, message: string, triggeredBy: list<string>}>
     */
    private function formatNoticeList(Report\NoticeList $noticeList): array
    {
        return \array_map(static function (Report\Notice $notice): array {
            return [
                'file' => $notice->file()->toString(),
                'line' => $notice->line()->toInt(),
                'message' => $notice->message()->toString(),
                'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                    return $testIdentifier->toString();
                }, $notice->triggeredBy()->toArray()),
            ];
        }, $noticeList->toArray());
    }

    /**
     * @return list<array{file: string, line: int, message: string, triggeredBy: list<string>}>
     */
    private function formatWarningList(Report\WarningList $warningList): array
    {
        return \array_map(static function (Report\Warning $warning): array {
            return [
                'file' => $warning->file()->toString(),
                'line' => $warning->line()->toInt(),
                'message' => $warning->message()->toString(),
                'triggeredBy' => \array_map(static function (Report\TestIdentifier $testIdentifier): string {
                    return $testIdentifier->toString();
                }, $warning->triggeredBy()->toArray()),
            ];
        }, $warningList->toArray());
    }
}
