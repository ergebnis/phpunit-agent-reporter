--TEST--
Extension does not replace output when infection/infection runs tests against a mutant
--ENV--
AI_AGENT=1
INFECTION=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit10/WithAiAgentAndInfection/Success/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s

%A
OK (2 tests, 2 assertions)
