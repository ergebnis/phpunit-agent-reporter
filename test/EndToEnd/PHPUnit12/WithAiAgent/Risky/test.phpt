--TEST--
Extension prints risky tests
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit12/WithAiAgent/Risky/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit12/WithAiAgent/Risky/phpunit.xml

OK (1 test, 0 assertions, 1 risky)

--- RISKY: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit12\WithAiAgent\Risky\ExampleTest::testNotPerformingAssertions
This test did not perform any assertions
