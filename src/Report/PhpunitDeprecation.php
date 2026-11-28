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
final class PhpunitDeprecation
{
    private function __construct(private readonly ?TestIdentifier $testIdentifier, private readonly ?File $file, private readonly ?Line $line, private readonly Message $message)
    {
    }

    public static function create(
        ?TestIdentifier $testIdentifier,
        ?File $file,
        ?Line $line,
        Message $message,
    ): self {
        return new self(
            $testIdentifier,
            $file,
            $line,
            $message,
        );
    }

    public function testIdentifier(): ?TestIdentifier
    {
        return $this->testIdentifier;
    }

    public function file(): ?File
    {
        return $this->file;
    }

    public function line(): ?Line
    {
        return $this->line;
    }

    public function message(): Message
    {
        return $this->message;
    }
}
