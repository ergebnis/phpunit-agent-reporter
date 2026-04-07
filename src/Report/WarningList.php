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
final class WarningList
{
    /**
     * @var list<Warning>
     */
    private readonly array $warnings;

    private function __construct(Warning ...$warnings)
    {
        $this->warnings = \array_values($warnings);
    }

    public static function create(Warning ...$warnings): self
    {
        return new self(...$warnings);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->warnings));
    }

    /**
     * @return list<Warning>
     */
    public function toArray(): array
    {
        return $this->warnings;
    }
}
