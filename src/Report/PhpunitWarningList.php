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
final class PhpunitWarningList
{
    /**
     * @var list<PhpunitWarning>
     */
    private readonly array $phpunitWarnings;

    private function __construct(PhpunitWarning ...$phpunitWarnings)
    {
        $this->phpunitWarnings = \array_values($phpunitWarnings);
    }

    public static function create(PhpunitWarning ...$phpunitWarnings): self
    {
        return new self(...$phpunitWarnings);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->phpunitWarnings));
    }

    /**
     * @return list<PhpunitWarning>
     */
    public function toArray(): array
    {
        return $this->phpunitWarnings;
    }
}
