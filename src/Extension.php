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

use Ergebnis\AgentDetector;
use PHPUnit\Runner;
use PHPUnit\TextUI;

final class Extension implements Runner\Extension\Extension
{
    public function bootstrap(
        TextUI\Configuration\Configuration $configuration,
        Runner\Extension\Facade $facade,
        Runner\Extension\ParameterCollection $parameters,
    ): void {
        $environment = \getenv();

        $detector = new AgentDetector\Detector();

        if (!$detector->isAgentPresent($environment)) {
            return;
        }

        /**
         * infection/infection sets this environment variable for every process that runs tests against a mutant, and
         * decides whether a mutant escaped by matching the default output of phpunit/phpunit.
         *
         * @see https://github.com/infection/infection/blob/0.27.11/src/Process/Runner/ParallelProcessRunner.php
         */
        if (\array_key_exists('INFECTION', $environment)) {
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
            new Reporter\JsonReporter(),
            $output,
        ));
    }
}
