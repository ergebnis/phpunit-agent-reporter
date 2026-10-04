--TEST--
Extension prints failures as they happen and summary line when tests fail
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit11/WithAiAgent/Failure/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit11/WithAiAgent/Failure/phpunit.xml


--- FAILURE: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit11\WithAiAgent\Failure\ExampleTest::testFailing
Failed asserting that false is true.

%s/test/EndToEnd/PHPUnit11/WithAiAgent/Failure/ExampleTest.php:27

--- FAILURE: Ergebnis\PHPUnit\AgentReporter\Test\EndToEnd\PHPUnit11\WithAiAgent\Failure\ExampleTest::testFailingStringComparison
Failed asserting that two strings are identical.
--- Expected
+++ Actual
@@ @@
-'foo'
+'bar'

%s/test/EndToEnd/PHPUnit11/WithAiAgent/Failure/ExampleTest.php:35

FAILURES (3 tests, 3 assertions, 2 failures)
