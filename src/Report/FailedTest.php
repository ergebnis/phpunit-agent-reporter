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
final class FailedTest
{
    private readonly TestIdentifier $testIdentifier;
    private readonly File $file;
    private readonly Line $line;
    private readonly Message $message;
    private readonly ?ComparisonFailure $comparisonFailure;

    private function __construct(
        TestIdentifier $testIdentifier,
        File $file,
        Line $line,
        Message $message,
        ?ComparisonFailure $comparisonFailure,
    ) {
        $this->testIdentifier = $testIdentifier;
        $this->file = $file;
        $this->line = $line;
        $this->message = $message;
        $this->comparisonFailure = $comparisonFailure;
    }

    public static function create(
        TestIdentifier $testIdentifier,
        File $file,
        Line $line,
        Message $message,
        ?ComparisonFailure $comparisonFailure,
    ): self {
        return new self(
            $testIdentifier,
            $file,
            $line,
            $message,
            $comparisonFailure,
        );
    }

    public function testIdentifier(): TestIdentifier
    {
        return $this->testIdentifier;
    }

    public function file(): File
    {
        return $this->file;
    }

    public function line(): Line
    {
        return $this->line;
    }

    public function message(): Message
    {
        return $this->message;
    }

    public function comparisonFailure(): ?ComparisonFailure
    {
        return $this->comparisonFailure;
    }
}
