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
final class FailedTestList
{
    /**
     * @var list<FailedTest>
     */
    private readonly array $values;

    private function __construct(FailedTest ...$values)
    {
        $this->values = \array_values($values);
    }

    public static function create(FailedTest ...$values): self
    {
        return new self(...$values);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->values));
    }

    /**
     * @return list<FailedTest>
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
