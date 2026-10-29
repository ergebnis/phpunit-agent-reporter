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

namespace Ergebnis\PHPUnit\AgentReporter\Subscriber\Application;

use Ergebnis\PHPUnit\AgentReporter\Reporter;
use PHPUnit\Event;
use PHPUnit\TestRunner;

/**
 * Reports the result when the application has finished, because phpunit/phpunit may still emit warnings after the test runner has finished.
 *
 * @internal
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Application.php
 */
final class ApplicationFinishedSubscriber implements Event\Application\FinishedSubscriber
{
    private readonly Reporter\ResultReporter $resultReporter;

    public function __construct(Reporter\ResultReporter $resultReporter)
    {
        $this->resultReporter = $resultReporter;
    }

    public function notify(Event\Application\Finished $event): void
    {
        $this->resultReporter->print(TestRunner\TestResult\Facade::result());
    }
}
