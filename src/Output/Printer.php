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

namespace Ergebnis\PHPUnit\AgentReporter\Output;

/**
 * @internal
 */
final class Printer
{
    /**
     * @var resource
     */
    private $output;
    private bool $lineIsOpen = false;

    /**
     * @param resource $output
     */
    public function __construct($output)
    {
        $this->output = $output;
    }

    public function print(string $text): void
    {
        if ('' === $text) {
            return;
        }

        if ($this->lineIsOpen) {
            $text = \PHP_EOL . $text;

            $this->lineIsOpen = false;
        }

        \fwrite(
            $this->output,
            $text,
        );
    }

    /**
     * Records text that phpunit/phpunit printed to the same stream, so that the next text starts on a new line when that text did not end with one.
     *
     * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Default/UnexpectedOutputPrinter.php
     */
    public function printedElsewhere(string $text): void
    {
        if ('' === $text) {
            return;
        }

        $this->lineIsOpen = !\str_ends_with(
            $text,
            "\n",
        );
    }
}
