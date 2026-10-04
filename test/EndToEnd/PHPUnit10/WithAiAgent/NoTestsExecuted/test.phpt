--TEST--
Extension prints that no tests were executed
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit10/WithAiAgent/NoTestsExecuted/phpunit.xml';
$_SERVER['argv'][] = '--filter=testDoesNotExist';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit10/WithAiAgent/NoTestsExecuted/phpunit.xml

No tests executed!
