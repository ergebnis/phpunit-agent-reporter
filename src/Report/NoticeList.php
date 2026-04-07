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
final class NoticeList
{
    /**
     * @var list<Notice>
     */
    private readonly array $notices;

    private function __construct(Notice ...$notices)
    {
        $this->notices = \array_values($notices);
    }

    public static function create(Notice ...$notices): self
    {
        return new self(...$notices);
    }

    public function count(): Count
    {
        return Count::fromInt(\count($this->notices));
    }

    /**
     * @return list<Notice>
     */
    public function toArray(): array
    {
        return $this->notices;
    }
}
