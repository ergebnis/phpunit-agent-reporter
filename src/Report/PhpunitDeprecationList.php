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
final class PhpunitDeprecationList
{
    /**
     * @var list<PhpunitDeprecation>
     */
    private readonly array $phpunitDeprecations;

    private function __construct(PhpunitDeprecation ...$phpunitDeprecations)
    {
        $this->phpunitDeprecations = \array_values($phpunitDeprecations);
    }

    public static function create(PhpunitDeprecation ...$phpunitDeprecations): self
    {
        return new self(...$phpunitDeprecations);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->phpunitDeprecations));
    }

    /**
     * @return list<PhpunitDeprecation>
     */
    public function toArray(): array
    {
        return $this->phpunitDeprecations;
    }
}
