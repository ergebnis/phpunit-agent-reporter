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

use PHPUnit\Event;

/**
 * @internal
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Compact/Renderer.php
 * @see https://github.com/sebastianbergmann/phpunit/pull/6597
 */
final class Renderer
{
    private readonly Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function nameOfTest(Event\Code\Test $test): string
    {
        if (!$test instanceof Event\Code\TestMethod) {
            return $test->name();
        }

        $testData = $test->testData();

        if (!$testData->hasDataFromDataProvider()) {
            return $test->nameWithClass();
        }

        return $test->className() . '::' . $test->methodName() . $testData->dataFromDataProvider()->dataAsStringForResultOutput();
    }

    /**
     * Renders the header line that starts a record ("--- TYPE: title").
     *
     * The header is always exactly one line, so that a user-supplied title cannot start a record of its own.
     */
    public function header(
        string $type,
        string $title,
    ): string {
        return \PHP_EOL . '--- ' . $type . ': ' . $this->singleLine($title) . \PHP_EOL;
    }

    /**
     * Renders the header line that starts a record without a title ("--- TYPE").
     */
    public function headerWithoutTitle(string $type): string
    {
        return \PHP_EOL . '--- ' . $type . \PHP_EOL;
    }

    /**
     * Renders one or more lines of user-supplied text that belong to the current record.
     */
    public function body(string $body): string
    {
        return $this->sanitizer->sanitize($body) . \PHP_EOL;
    }

    public function throwable(Event\Code\Throwable $throwable): string
    {
        $rendered = $this->body(\trim($throwable->description())) . $this->stackTrace($throwable->stackTrace());

        if ($throwable->hasPrevious()) {
            $rendered .= 'Caused by' . \PHP_EOL . $this->throwable($throwable->previous());
        }

        return $rendered;
    }

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
