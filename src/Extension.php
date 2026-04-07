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

namespace Ergebnis\PHPUnit\AgentReporter;

use PHPUnit\Runner;
use PHPUnit\TextUI;

final class Extension implements Runner\Extension\Extension
{
    public function bootstrap(
        TextUI\Configuration\Configuration $configuration,
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        $detector = new Agent\Detector();

        $isAgent = $detector->isAgent(\getenv());

        if (!$isAgent) {
            return;
        }

        $facade->replaceOutput();

        $target = 'php://stdout';

        if ($configuration->outputToStandardErrorStream()) {
            $target = 'php://stderr';
        }

        $output = \fopen(
            $target,
            'wb',
        );

        if (!\is_resource($output)) {
            return;
        }

        $facade->registerSubscribers(new Subscriber\Application\ApplicationFinishedSubscriber(
            new Report\TestResultTranslator(),
            new Reporter\JsonReporter($configuration),
            $output,
        ));
    }
}
