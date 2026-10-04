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

use Ergebnis\PHPUnit\AgentReporter\Output;
use PHPUnit\Event;
use PHPUnit\TestRunner;

/**
 * Prints the result when the application has finished, because phpunit/phpunit may still emit warnings after the test runner has finished.
 *
 * @internal
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Application.php
 */
final class ApplicationFinishedSubscriber implements Event\Application\FinishedSubscriber
{
    private readonly Output\ResultPrinter $resultPrinter;

    public function __construct(Output\ResultPrinter $resultPrinter)
    {
        $this->resultPrinter = $resultPrinter;
    }

    public function notify(Event\Application\Finished $event): void
    {
        $this->resultPrinter->print(TestRunner\TestResult\Facade::result());
    }
}
