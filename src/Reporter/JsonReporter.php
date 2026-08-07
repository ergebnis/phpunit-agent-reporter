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

/**
 * @internal
 */
final class JsonReporter implements Reporter
{
    public function report(Report\Report $report): string
    {
        $zero = Report\Count::zero();

        $summary = [
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

        if ($report->deprecationList()->count()->isGreaterThan($zero)) {
            $details['deprecations'] = $this->formatDeprecationList($report->deprecationList());
        }

        if ($report->incompleteTestList()->count()->isGreaterThan($zero)) {
            $details['incomplete'] = \array_map(static function (Report\IncompleteTest $incompleteTest): array {
                return [
                    'file' => $incompleteTest->file()->toString(),
                    'line' => $incompleteTest->line()->toInt(),
                    'message' => $incompleteTest->message()->toString(),
                    'test' => $incompleteTest->testIdentifier()->toString(),
                ];
            }, $report->incompleteTestList()->toArray());
        }

        if ($report->noticeList()->count()->isGreaterThan($zero)) {
            $details['notices'] = $this->formatNoticeList($report->noticeList());
        }

        if ($report->phpunitDeprecationList()->count()->isGreaterThan($zero)) {
            $details['phpunitDeprecations'] = \array_map(static function (Report\PhpunitDeprecation $phpunitDeprecation): array {
                return self::formatPhpunitIssue(
                    $phpunitDeprecation->testIdentifier(),
                    $phpunitDeprecation->file(),
                    $phpunitDeprecation->line(),
                    $phpunitDeprecation->message(),
                );
            }, $report->phpunitDeprecationList()->toArray());
        }

        if ($report->phpunitNoticeList()->count()->isGreaterThan($zero)) {
            $details['phpunitNotices'] = \array_map(static function (Report\PhpunitNotice $phpunitNotice): array {
                return self::formatPhpunitIssue(
                    $phpunitNotice->testIdentifier(),
                    $phpunitNotice->file(),
                    $phpunitNotice->line(),
                    $phpunitNotice->message(),
                );
            }, $report->phpunitNoticeList()->toArray());
        }

        if ($report->phpunitWarningList()->count()->isGreaterThan($zero)) {
            $details['phpunitWarnings'] = \array_map(static function (Report\PhpunitWarning $phpunitWarning): array {
                return self::formatPhpunitIssue(
                    $phpunitWarning->testIdentifier(),
                    $phpunitWarning->file(),
                    $phpunitWarning->line(),
                    $phpunitWarning->message(),
                );
            }, $report->phpunitWarningList()->toArray());
        }

        if ($report->riskyTestList()->count()->isGreaterThan($zero)) {
            $details['risky'] = \array_map(static function (Report\RiskyTest $riskyTest): array {
                return [
                    'file' => $riskyTest->file()->toString(),
                    'line' => $riskyTest->line()->toInt(),
                    'message' => $riskyTest->message()->toString(),
                    'test' => $riskyTest->testIdentifier()->toString(),
                ];
            }, $report->riskyTestList()->toArray());
        }

        if ($report->skippedTestList()->count()->isGreaterThan($zero)) {
            $details['skipped'] = \array_map(static function (Report\SkippedTest $skippedTest): array {
                return [
                    'file' => $skippedTest->file()->toString(),
                    'line' => $skippedTest->line()->toInt(),
                    'message' => $skippedTest->message()->toString(),
                    'test' => $skippedTest->testIdentifier()->toString(),
                ];
            }, $report->skippedTestList()->toArray());
        }

        if ($report->warningList()->count()->isGreaterThan($zero)) {
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
     * @return array{
     *     file?: string,
     *     line?: int,
     *     message: string,
     *     test?: string
     * }
     */
    private static function formatPhpunitIssue(
        ?Report\TestIdentifier $testIdentifier,
        ?Report\File $file,
        ?Report\Line $line,
        Report\Message $message,
    ): array {
        $item = [
            'message' => $message->toString(),
        ];

        if ($testIdentifier instanceof Report\TestIdentifier) {
            $item['test'] = $testIdentifier->toString();
        }

        if ($file instanceof Report\File) {
            $item['file'] = $file->toString();
        }

        if ($line instanceof Report\Line) {
            $item['line'] = $line->toInt();
        }

        \ksort($item);

        return $item;
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
