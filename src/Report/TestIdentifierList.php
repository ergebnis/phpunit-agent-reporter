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
final class TestIdentifierList
{
    /**
     * @var list<TestIdentifier>
     */
    private readonly array $testIdentifiers;

    private function __construct(TestIdentifier ...$testIdentifiers)
    {
        $this->testIdentifiers = \array_values($testIdentifiers);
    }

    public static function create(TestIdentifier ...$testIdentifiers): self
    {
        return new self(...$testIdentifiers);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->testIdentifiers));
    }

    /**
     * @return list<TestIdentifier>
     */
    public function toArray(): array
    {
        return $this->testIdentifiers;
    }
}
