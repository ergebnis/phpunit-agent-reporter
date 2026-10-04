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

namespace Ergebnis\PHPUnit\AgentReporter\Reporter;

use Ergebnis\PHPUnit\AgentReporter\Output;
use PHPUnit\Event;

/**
 * Renders the names of tests and throwables from events of phpunit/phpunit with the compact renderer.
 *
 * @internal
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Compact/Renderer.php
 */
final class EventRenderer
{
    private readonly Output\CompactRenderer $compactRenderer;

    public function __construct(Output\CompactRenderer $compactRenderer)
    {
        $this->compactRenderer = $compactRenderer;
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

    public function throwable(Event\Code\Throwable $throwable): string
    {
        $rendered = $this->compactRenderer->body(\trim($throwable->description())) . $this->compactRenderer->stackTrace($throwable->stackTrace());

        if ($throwable->hasPrevious()) {
            $rendered .= 'Caused by' . \PHP_EOL . $this->throwable($throwable->previous());
        }

        return $rendered;
    }
}
