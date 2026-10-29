--TEST--
Extension prints deprecations, notices, and warnings when details are displayed
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit13/WithAiAgent/IssuesWithDetails/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %s/test/EndToEnd/PHPUnit13/WithAiAgent/IssuesWithDetails/phpunit.xml

OK (3 tests, 3 assertions, 1 deprecation, 1 warning, 1 notice)

--- DEPRECATION: %s/test/EndToEnd/PHPUnit13/WithAiAgent/IssuesWithDetails/ExampleTest.php:%d
Something is deprecated.

--- WARNING: %s/test/EndToEnd/PHPUnit13/WithAiAgent/IssuesWithDetails/ExampleTest.php:%d
Something is dangerous.

--- NOTICE: %s/test/EndToEnd/PHPUnit13/WithAiAgent/IssuesWithDetails/ExampleTest.php:%d
Something is noteworthy.
