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
final class Notice
{
    private readonly File $file;
    private readonly Line $line;
    private readonly Message $message;
    private readonly TestIdentifierList $triggeredBy;

    private function __construct(
        File $file,
        Line $line,
        Message $message,
        TestIdentifierList $triggeredBy,
    ) {
        $this->file = $file;
        $this->line = $line;
        $this->message = $message;
        $this->triggeredBy = $triggeredBy;
    }

    public static function create(
        File $file,
        Line $line,
        Message $message,
        TestIdentifierList $triggeredBy,
    ): self {
        return new self(
            $file,
            $line,
            $message,
            $triggeredBy,
        );
    }

    public function file(): File
    {
        return $this->file;
    }

    public function line(): Line
    {
        return $this->line;
    }

    public function message(): Message
    {
        return $this->message;
    }

    public function triggeredBy(): TestIdentifierList
    {
        return $this->triggeredBy;
    }
}
