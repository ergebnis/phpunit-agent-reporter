--TEST--
Extension prints errors as they happen and summary line when tests error
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit13/WithAiAgent/Error/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit13/WithAiAgent/Error/phpunit.xml


--- ERROR: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit13\WithAiAgent\Error\ExampleTest::testErroring
RuntimeException: Something went wrong.

%s/test/EndToEnd/PHPUnit13/WithAiAgent/Error/ExampleTest.php:27
Caused by
LogicException: Something else went wrong before.

%s/test/EndToEnd/PHPUnit13/WithAiAgent/Error/ExampleTest.php:30

ERRORS (2 tests, 1 assertion, 1 error)
