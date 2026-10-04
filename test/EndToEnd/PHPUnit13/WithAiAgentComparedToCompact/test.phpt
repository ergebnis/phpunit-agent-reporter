--TEST--
Extension prints the same output as phpunit/phpunit with compact output enabled
--SKIPIF--
<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../vendor/autoload.php';

/**
 * phpunit/phpunit prints output printed by tests as a record of its own only since 13.4.0.
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/13.4.0/src/TextUI/Output/Compact/ProgressPrinter/ProgressPrinter.php
 */
if (\version_compare(PHPUnit\Runner\Version::id(), '13.4.0', '<')) {
    echo 'skip: phpunit/phpunit prints compact output for output printed by tests only since 13.4.0';
}
--ENV--
AI_AGENT=1
--FILE--
<?php

declare(strict_types=1);

$phpunit = __DIR__ . '/../../../../vendor/bin/phpunit';

$directories = \glob(__DIR__ . '/../WithAiAgent/*', \GLOB_ONLYDIR);

if (!\is_array($directories)) {
    exit(1);
}

foreach ($directories as $directory) {
    $command = \sprintf(
        '%s %s --configuration=%s',
        \escapeshellarg(\PHP_BINARY),
        \escapeshellarg($phpunit),
        \escapeshellarg($directory . '/phpunit.xml'),
    );

    $testFile = \file_get_contents($directory . '/test.phpt');

    if (
        \is_string($testFile)
        && 1 === \preg_match('/--filter=(\w+)/', $testFile, $matches)
    ) {
        $command .= ' ' . \escapeshellarg('--filter=' . $matches[1]);
    }

    $output = \shell_exec($command . ' 2>&1');
    $compactOutput = \shell_exec($command . ' --compact 2>&1');

    $result = 'identical';

    if ($output !== $compactOutput) {
        $result = \sprintf(
            "different\n\nwith extension:\n\n%s\n\nwith --compact:\n\n%s",
            $output,
            $compactOutput,
        );
    }

    echo \sprintf(
        '%s: %s',
        \basename($directory),
        $result,
    ) . \PHP_EOL;
}
--EXPECT--
Error: identical
Failure: identical
Issues: identical
IssuesWithDetails: identical
NoTestsExecuted: identical
Output: identical
PhpunitNotice: identical
Risky: identical
SetUpBeforeClassError: identical
SkippedAndIncomplete: identical
SkippedAndIncompleteWithDetails: identical
Success: identical
TearDownAfterClassFailure: identical
