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

use Ergebnis\PHPUnit\AgentReporter\Reporter;
use PHPUnit\Event;

/**
 * @internal
 */
final class TestErroredSubscriber implements Event\Test\ErroredSubscriber
{
    private readonly Reporter\ProgressReporter $progressReporter;

    public function __construct(Reporter\ProgressReporter $progressReporter)
    {
        $this->progressReporter = $progressReporter;
    }

    public function notify(Event\Test\Errored $event): void
    {
        $this->progressReporter->testErrored(
            $event->test(),
            $event->throwable(),
        );
    }
}
