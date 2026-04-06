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
final class ErroredTestList
{
    /**
     * @var list<ErroredTest>
     */
    private readonly array $erroredTests;

    private function __construct(ErroredTest ...$erroredTests)
    {
        $this->erroredTests = \array_values($erroredTests);
    }

    public static function create(ErroredTest ...$erroredTests): self
    {
        return new self(...$erroredTests);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->erroredTests));
    }

    /**
     * @return list<ErroredTest>
     */
    public function toArray(): array
    {
        return $this->erroredTests;
    }
}
