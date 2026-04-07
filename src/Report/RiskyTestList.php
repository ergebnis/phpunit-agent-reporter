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
final class RiskyTestList
{
    /**
     * @var list<RiskyTest>
     */
    private readonly array $riskyTests;

    private function __construct(RiskyTest ...$riskyTests)
    {
        $this->riskyTests = \array_values($riskyTests);
    }

    public static function create(RiskyTest ...$riskyTests): self
    {
        return new self(...$riskyTests);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->riskyTests));
    }

    /**
     * @return list<RiskyTest>
     */
    public function toArray(): array
    {
        return $this->riskyTests;
    }
}
