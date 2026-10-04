--TEST--
Extension prints skipped and incomplete tests when details are displayed
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit11/WithAiAgent/SkippedAndIncompleteWithDetails/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit11/WithAiAgent/SkippedAndIncompleteWithDetails/phpunit.xml

OK (2 tests, 0 assertions, 1 skipped, 1 incomplete)

--- INCOMPLETE: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit11\WithAiAgent\SkippedAndIncompleteWithDetails\ExampleTest::testIncomplete
Not yet implemented.

--- SKIPPED: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit11\WithAiAgent\SkippedAndIncompleteWithDetails\ExampleTest::testSkipped
Skipped for demonstration purposes.
