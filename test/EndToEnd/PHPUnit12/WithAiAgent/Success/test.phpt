--TEST--
Extension outputs JSON with result success when all tests pass
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit12/WithAiAgent/Success/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
{
    "result": "success",
    "summary": {
        "assertions": 2,
        "errors": 0,
        "failures": 0,
        "tests": 2,
        "warnings": 0
    }
}
