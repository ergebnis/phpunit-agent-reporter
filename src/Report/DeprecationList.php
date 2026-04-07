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
final class DeprecationList
{
    /**
     * @var list<Deprecation>
     */
    private readonly array $deprecations;

    private function __construct(Deprecation ...$deprecations)
    {
        $this->deprecations = \array_values($deprecations);
    }

    public static function create(Deprecation ...$deprecations): self
    {
        return new self(...$deprecations);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->deprecations));
    }

    /**
     * @return list<Deprecation>
     */
    public function toArray(): array
    {
        return $this->deprecations;
    }
}
