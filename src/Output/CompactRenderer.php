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
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Compact/Renderer.php
 * @see https://github.com/sebastianbergmann/phpunit/pull/6597
 */
final class CompactRenderer
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    /**
     * Renders the header line that starts a record ("--- TYPE: title").
     *
     * The header is always exactly one line, so that a title cannot start a record of its own.
     */
    public function header(
        string $type,
        string $title,
    ): string {
        return \PHP_EOL . \sprintf(
            '--- %s: %s',
            $type,
            $this->singleLine($title),
        ) . \PHP_EOL;
    }

    /**
     * Renders the header line that starts a record without a title ("--- TYPE").
     */
    public function headerWithoutTitle(string $type): string
    {
        return \PHP_EOL . \sprintf(
            '--- %s',
            $type,
        ) . \PHP_EOL;
    }

    /**
     * Renders one or more lines of text that belong to the current record.
     */
    public function body(string $body): string
    {
        return $this->sanitizer->sanitize($body) . \PHP_EOL;
    }

    /**
     * Renders a stack trace preceded by a blank line, or nothing when the stack trace is blank.
     */
    public function stackTrace(string $stackTrace): string
    {
        $stackTrace = \trim($stackTrace);

        if ('' === $stackTrace) {
            return '';
        }

        return \PHP_EOL . $this->body($stackTrace);
    }

    private function singleLine(string $text): string
    {
        return \str_replace(
            [
                "\r",
                "\n",
            ],
            [
                '\u{000D}',
                '\u{000A}',
            ],
            $this->sanitizer->sanitize($text),
        );
    }
}
