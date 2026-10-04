--TEST--
Extension prints failure when an assertion fails in tearDownAfterClass()
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit10/WithAiAgent/TearDownAfterClassFailure/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit10/WithAiAgent/TearDownAfterClassFailure/phpunit.xml


--- FAILURE: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit10\WithAiAgent\TearDownAfterClassFailure\ExampleTest
Failed asserting that false is true.

%s/test/EndToEnd/PHPUnit10/WithAiAgent/TearDownAfterClassFailure/ExampleTest.php:22

ERRORS (1 test, 1 assertion, 1 error)
