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

use Ergebnis\PHPUnit\AgentReporter\Reporter;
use PHPUnit\Event;

/**
 * @internal
 */
final class TestRunnerExecutionFinishedSubscriber implements Event\TestRunner\ExecutionFinishedSubscriber
{
    private readonly Reporter\ProgressReporter $progressReporter;

    public function __construct(Reporter\ProgressReporter $progressReporter)
    {
        $this->progressReporter = $progressReporter;
    }

    public function notify(Event\TestRunner\ExecutionFinished $event): void
    {
        $this->progressReporter->testRunnerExecutionFinished();
    }
}
