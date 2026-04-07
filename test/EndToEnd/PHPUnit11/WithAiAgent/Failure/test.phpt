--TEST--
Extension outputs JSON with result failure when tests fail
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
{
    "result": "failure",
    "summary": {
        "assertions": 2,
        "errors": 0,
        "failures": 1,
        "tests": 2,
        "warnings": 0
    },
    "details": {
        "failures": [
            {
                "file": "%s/test/EndToEnd/PHPUnit11/WithAiAgent/Failure/ExampleTest.php",
                "line": %d,
                "message": "Failed asserting that false is true.",
                "test": "Ergebnis\\PHPUnit\\AgentReporter\\Test\\EndToEnd\\PHPUnit11\\WithAiAgent\\Failure\\ExampleTest::testFailing"
            }
        ]
    }
}
