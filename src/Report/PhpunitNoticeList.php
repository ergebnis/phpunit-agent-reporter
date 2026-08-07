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
final class PhpunitNoticeList
{
    /**
     * @var list<PhpunitNotice>
     */
    private readonly array $phpunitNotices;

    private function __construct(PhpunitNotice ...$phpunitNotices)
    {
        $this->phpunitNotices = \array_values($phpunitNotices);
    }

    public static function create(PhpunitNotice ...$phpunitNotices): self
    {
        return new self(...$phpunitNotices);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->phpunitNotices));
    }

    /**
     * @return list<PhpunitNotice>
     */
    public function toArray(): array
    {
        return $this->phpunitNotices;
    }
}
