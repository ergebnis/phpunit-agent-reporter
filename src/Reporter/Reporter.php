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

use Ergebnis\PHPUnit\AgentReporter\Report;

/**
 * @internal
 */
interface Reporter
{
    public function report(Report\Report $report): string;
}
