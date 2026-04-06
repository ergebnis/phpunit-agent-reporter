--TEST--
With default configuration
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=test/EndToEnd/PHPUnit10/WithoutAiAgent/phpunit.xml';

require_once __DIR__ . '/../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime:       PHP %s
Configuration: %Stest/EndToEnd/PHPUnit10/WithoutAiAgent/phpunit.xml

.FESIFFF                                                            8 / 8 (100%)
%A
ERRORS!
Tests: 8, Assertions: 5, Errors: 1, Failures: 4%S, Skipped: 1, Incomplete: 1.
