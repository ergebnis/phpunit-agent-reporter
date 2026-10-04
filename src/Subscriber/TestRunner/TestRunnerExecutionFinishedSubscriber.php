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

namespace Ergebnis\PHPUnit\AgentReporter\Subscriber\TestRunner;

use Ergebnis\PHPUnit\AgentReporter\Output;
use PHPUnit\Event;

/**
 * @internal
 */
final class TestRunnerExecutionFinishedSubscriber implements Event\TestRunner\ExecutionFinishedSubscriber
{
    private readonly Output\ProgressPrinter $progressPrinter;

    public function __construct(Output\ProgressPrinter $progressPrinter)
    {
        $this->progressPrinter = $progressPrinter;
    }

    public function notify(Event\TestRunner\ExecutionFinished $event): void
    {
        $this->progressPrinter->testRunnerExecutionFinished();
    }
}
