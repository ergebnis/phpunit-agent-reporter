--TEST--
Extension outputs JSON with result exception when tests error
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit12/WithAiAgent/Exception/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
{
    "result": "exception",
    "summary": {
        "errors": 1,
        "failures": 1,
        "tests": 5,
        "warnings": 0
    },
    "details": {
        "errors": [
            {
                "file": "%s/test/EndToEnd/PHPUnit12/WithAiAgent/Exception/ExampleTest.php",
                "line": %d,
                "message": "Something went wrong.",
                "test": "Ergebnis\\PHPUnit\\AgentReporter\\Test\\EndToEnd\\PHPUnit12\\WithAiAgent\\Exception\\ExampleTest::testErroring"
            }
        ],
        "failures": [
            {
                "file": "%s/test/EndToEnd/PHPUnit12/WithAiAgent/Exception/ExampleTest.php",
                "line": %d,
                "message": "Failed asserting that false is true.",
                "test": "Ergebnis\\PHPUnit\\AgentReporter\\Test\\EndToEnd\\PHPUnit12\\WithAiAgent\\Exception\\ExampleTest::testFailing"
            }
        ]
    }
}
