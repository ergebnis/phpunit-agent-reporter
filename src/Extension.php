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
use PHPUnit\Event;
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

        /**
         * phpunit/phpunit prints compact output itself when it is enabled with the --compact option or the PHPUNIT_COMPACT_OUTPUT environment variable, available since 13.2.0.
         *
         * @see https://github.com/sebastianbergmann/phpunit/pull/6597
         * @see https://github.com/sebastianbergmann/phpunit/blob/13.2.0/src/TextUI/Configuration/Configuration.php
         */
        if (
            \method_exists($configuration, 'outputIsCompact')
            && true === $configuration->outputIsCompact()
        ) {
            return;
        }

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

        /**
         * Replacing progress and result output instead of all output lets phpunit/phpunit print its header, as it does for compact output.
         *
         * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Application.php
         */
        $facade->replaceProgressOutput();
        $facade->replaceResultOutput();

        $printer = new Output\Printer($output);
        $renderer = new Output\Renderer(new Output\Sanitizer());

        $progressPrinter = new Output\ProgressPrinter(
            $printer,
            $renderer,
            !$configuration->disallowTestOutput(),
        );

        $displayDetailsOnAllIssues = $configuration->displayDetailsOnAllIssues();

        $displayDetailsOnPhpunitNotices = $displayDetailsOnAllIssues;

        /**
         * phpunit/phpunit provides the --display-phpunit-notices option only since 12.1.0.
         *
         * @see https://github.com/sebastianbergmann/phpunit/blob/12.1.0/src/TextUI/Configuration/Configuration.php
         */
        if (
            \method_exists($configuration, 'displayDetailsOnPhpunitNotices')
            && true === $configuration->displayDetailsOnPhpunitNotices()
        ) {
            $displayDetailsOnPhpunitNotices = true;
        }

        $resultPrinter = new Output\ResultPrinter(
            $printer,
            $renderer,
            $displayDetailsOnAllIssues || $configuration->displayDetailsOnIncompleteTests(),
            $displayDetailsOnAllIssues || $configuration->displayDetailsOnSkippedTests(),
            $displayDetailsOnAllIssues || $configuration->displayDetailsOnTestsThatTriggerDeprecations(),
            $displayDetailsOnAllIssues || $configuration->displayDetailsOnTestsThatTriggerErrors(),
            $displayDetailsOnAllIssues || $configuration->displayDetailsOnTestsThatTriggerNotices(),
            $displayDetailsOnAllIssues || $configuration->displayDetailsOnTestsThatTriggerWarnings(),
            $displayDetailsOnAllIssues || $configuration->displayDetailsOnPhpunitDeprecations(),
            $displayDetailsOnPhpunitNotices,
        );

        $facade->registerSubscribers(
            new Subscriber\Test\TestPreparationStartedSubscriber($progressPrinter),
            new Subscriber\Test\TestErroredSubscriber($progressPrinter),
            new Subscriber\Test\TestFailedSubscriber($progressPrinter),
            new Subscriber\Test\TestPrintedUnexpectedOutputSubscriber($progressPrinter),
            new Subscriber\Test\BeforeFirstTestMethodErroredSubscriber($progressPrinter),
            new Subscriber\Test\AfterLastTestMethodErroredSubscriber($progressPrinter),
            new Subscriber\TestRunner\TestRunnerExecutionFinishedSubscriber($progressPrinter),
            new Subscriber\Application\ApplicationFinishedSubscriber($resultPrinter),
        );

        /**
         * phpunit/phpunit emits dedicated events for assertion failures in hook methods only since 12.2.0.
         *
         * @see https://github.com/sebastianbergmann/phpunit/blob/12.2.0/src/Event/Events/Test/HookMethod/BeforeFirstTestMethodFailed.php
         */
        if (\interface_exists(Event\Test\BeforeFirstTestMethodFailedSubscriber::class)) {
            $facade->registerSubscribers(
                new Subscriber\Test\BeforeFirstTestMethodFailedSubscriber($progressPrinter),
                new Subscriber\Test\AfterLastTestMethodFailedSubscriber($progressPrinter),
            );
        }
    }
}
