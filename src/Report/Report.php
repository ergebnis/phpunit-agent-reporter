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

/**
 * @internal
 */
final class Report
{
    private readonly ShellExitCode $shellExitCode;
    private readonly ErroredTestList $erroredTestList;
    private readonly FailedTestList $failedTestList;
    private readonly IncompleteTestList $incompleteTestList;
    private readonly SkippedTestList $skippedTestList;
    private readonly RiskyTestList $riskyTestList;
    private readonly DeprecationList $deprecationList;
    private readonly NoticeList $noticeList;
    private readonly WarningList $warningList;
    private readonly Count $totalAssertionCount;
    private readonly Count $totalTestCount;

    private function __construct(
        ShellExitCode $shellExitCode,
        ErroredTestList $erroredTestList,
        FailedTestList $failedTestList,
        IncompleteTestList $incompleteTestList,
        SkippedTestList $skippedTestList,
        RiskyTestList $riskyTestList,
        DeprecationList $deprecationList,
        NoticeList $noticeList,
        WarningList $warningList,
        Count $totalAssertionCount,
        Count $totalTestCount,
    ) {
        $this->shellExitCode = $shellExitCode;
        $this->erroredTestList = $erroredTestList;
        $this->failedTestList = $failedTestList;
        $this->incompleteTestList = $incompleteTestList;
        $this->skippedTestList = $skippedTestList;
        $this->riskyTestList = $riskyTestList;
        $this->deprecationList = $deprecationList;
        $this->noticeList = $noticeList;
        $this->warningList = $warningList;
        $this->totalAssertionCount = $totalAssertionCount;
        $this->totalTestCount = $totalTestCount;
    }

    public static function create(
        ShellExitCode $shellExitCode,
        ErroredTestList $erroredTestList,
        FailedTestList $failedTestList,
        IncompleteTestList $incompleteTestList,
        SkippedTestList $skippedTestList,
        RiskyTestList $riskyTestList,
        DeprecationList $deprecationList,
        NoticeList $noticeList,
        WarningList $warningList,
        Count $totalAssertionCount,
        Count $totalTestCount,
    ): self {
        return new self(
            $shellExitCode,
            $erroredTestList,
            $failedTestList,
            $incompleteTestList,
            $skippedTestList,
            $riskyTestList,
            $deprecationList,
            $noticeList,
            $warningList,
            $totalAssertionCount,
            $totalTestCount,
        );
    }

    public function result(): Result
    {
        if ($this->shellExitCode->equals(ShellExitCode::success())) {
            return Result::success();
        }

        if ($this->shellExitCode->equals(ShellExitCode::exception())) {
            return Result::exception();
        }

        return Result::failure();
    }

    public function shellExitCode(): ShellExitCode
    {
        return $this->shellExitCode;
    }

    public function erroredTestList(): ErroredTestList
    {
        return $this->erroredTestList;
    }

    public function failedTestList(): FailedTestList
    {
        return $this->failedTestList;
    }

    public function incompleteTestList(): IncompleteTestList
    {
        return $this->incompleteTestList;
    }

    public function skippedTestList(): SkippedTestList
    {
        return $this->skippedTestList;
    }

    public function riskyTestList(): RiskyTestList
    {
        return $this->riskyTestList;
    }

    public function deprecationList(): DeprecationList
    {
        return $this->deprecationList;
    }

    public function noticeList(): NoticeList
    {
        return $this->noticeList;
    }

    public function warningList(): WarningList
    {
        return $this->warningList;
    }

    public function totalAssertionCount(): Count
    {
        return $this->totalAssertionCount;
    }

    public function totalTestCount(): Count
    {
        return $this->totalTestCount;
    }
}
