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

namespace Ergebnis\PHPUnit\AgentReporter\Subscriber\Test;

use Ergebnis\PHPUnit\AgentReporter\Output;
use PHPUnit\Event;

/**
 * @internal
 */
final class BeforeFirstTestMethodErroredSubscriber implements Event\Test\BeforeFirstTestMethodErroredSubscriber
{
    private readonly Output\ProgressPrinter $progressPrinter;

    public function __construct(Output\ProgressPrinter $progressPrinter)
    {
        $this->progressPrinter = $progressPrinter;
    }

    public function notify(Event\Test\BeforeFirstTestMethodErrored $event): void
    {
        $this->progressPrinter->hookMethodErrored(
            $event->testClassName(),
            $event->throwable(),
        );
    }
}
