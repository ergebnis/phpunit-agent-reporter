--TEST--
Extension prints header for output printed by tests
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit11/WithAiAgent/Output/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit11/WithAiAgent/Output/phpunit.xml


--- OUTPUT: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit11\WithAiAgent\Output\ExampleTest::testPrintingOutput
Hello, World!

OK (1 test, 1 assertion)
