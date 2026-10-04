--TEST--
Extension counts PHPUnit notices without details when details are not displayed
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
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit13/WithAiAgent/PhpunitNotice/phpunit.xml

OK (1 test, 1 assertion, 1 PHPUnit notice)
