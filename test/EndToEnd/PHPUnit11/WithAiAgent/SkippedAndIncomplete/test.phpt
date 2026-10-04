--TEST--
Extension counts skipped and incomplete tests without details when details are not displayed
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit11/WithAiAgent/SkippedAndIncomplete/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit11/WithAiAgent/SkippedAndIncomplete/phpunit.xml

OK (2 tests, 0 assertions, 1 skipped, 1 incomplete)
