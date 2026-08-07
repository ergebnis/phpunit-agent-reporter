--TEST--
Extension outputs JSON with phpunitNotices when a test triggers a PHPUnit notice
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit13/WithAiAgent/PhpunitNotice/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
{
    "result": "success",
    "summary": {
        "assertions": %d,
        "deprecations": 0,
        "errors": 0,
        "failures": 0,
        "incomplete": 0,
        "notices": 0,
        "phpunitDeprecations": 0,
        "phpunitNotices": 1,
        "phpunitWarnings": 0,
        "risky": 0,
        "skipped": 0,
        "tests": 1,
        "warnings": 0
    },
    "details": {
        "phpunitNotices": [
            {
                "file": "%s/test/EndToEnd/PHPUnit13/WithAiAgent/PhpunitNotice/ExampleTest.php",
                "line": %d,
                "message": "No expectations were configured for the mock object for Countable. %s",
                "test": "Ergebnis\\PHPUnit\\AgentReporter\\Test\\EndToEnd\\PHPUnit13\\WithAiAgent\\PhpunitNotice\\ExampleTest::testTriggeringPhpunitNotice"
            }
        ]
    }
}
