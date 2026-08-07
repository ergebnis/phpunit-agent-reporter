--TEST--
Extension outputs JSON with result exception when tests error
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit11/WithAiAgent/Exception/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
{
    "result": "exception",
    "summary": {
        "assertions": 2,
        "deprecations": 0,
        "errors": 1,
        "failures": 1,
        "incomplete": 1,
        "notices": 0,
        "phpunitDeprecations": 0,
        "phpunitNotices": 0,
        "phpunitWarnings": 0,
        "risky": 0,
        "skipped": 1,
        "tests": 5,
        "warnings": 0
    },
    "details": {
        "errors": [
            {
                "file": "%s/test/EndToEnd/PHPUnit11/WithAiAgent/Exception/ExampleTest.php",
                "line": %d,
                "message": "Something went wrong.",
                "test": "Ergebnis\\PHPUnit\\AgentReporter\\Test\\EndToEnd\\PHPUnit11\\WithAiAgent\\Exception\\ExampleTest::testErroring"
            }
        ],
        "failures": [
            {
                "file": "%s/test/EndToEnd/PHPUnit11/WithAiAgent/Exception/ExampleTest.php",
                "line": %d,
                "message": "Failed asserting that false is true.",
                "test": "Ergebnis\\PHPUnit\\AgentReporter\\Test\\EndToEnd\\PHPUnit11\\WithAiAgent\\Exception\\ExampleTest::testFailing"
            }
        ],
        "incomplete": [
            {
                "file": "%s/test/EndToEnd/PHPUnit11/WithAiAgent/Exception/ExampleTest.php",
                "line": %d,
                "message": "Not yet implemented.",
                "test": "Ergebnis\\PHPUnit\\AgentReporter\\Test\\EndToEnd\\PHPUnit11\\WithAiAgent\\Exception\\ExampleTest::testIncomplete"
            }
        ],
        "skipped": [
            {
                "file": "%s/test/EndToEnd/PHPUnit11/WithAiAgent/Exception/ExampleTest.php",
                "line": %d,
                "message": "Skipped for demonstration purposes.",
                "test": "Ergebnis\\PHPUnit\\AgentReporter\\Test\\EndToEnd\\PHPUnit11\\WithAiAgent\\Exception\\ExampleTest::testSkipped"
            }
        ]
    }
}
