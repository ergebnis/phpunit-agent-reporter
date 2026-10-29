--TEST--
Extension prints error when setUpBeforeClass() errors
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit13/WithAiAgent/SetUpBeforeClassError/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit13/WithAiAgent/SetUpBeforeClassError/phpunit.xml


--- ERROR: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit13\WithAiAgent\SetUpBeforeClassError\ExampleTest
RuntimeException: Something went wrong before the first test.

%s/test/EndToEnd/PHPUnit13/WithAiAgent/SetUpBeforeClassError/ExampleTest.php:22

ERRORS (1 test, 0 assertions, 1 error)
