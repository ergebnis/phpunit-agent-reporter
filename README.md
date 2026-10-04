# phpunit-agent-reporter

[![Integrate](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/integrate.yaml/badge.svg?branch=main)](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/integrate.yaml)
[![Merge](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/merge.yaml/badge.svg)](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/merge.yaml)
[![Nightly](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/nightly.yaml/badge.svg)](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/nightly.yaml)
[![Release](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/release.yaml/badge.svg)](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/release.yaml)
[![Renew](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/renew.yaml/badge.svg)](https://github.com/ergebnis/phpunit-agent-reporter/actions/workflows/renew.yaml)

[![Code Coverage](https://codecov.io/gh/ergebnis/phpunit-agent-reporter/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/phpunit-agent-reporter)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/phpunit-agent-reporter/v/stable)](https://packagist.org/packages/ergebnis/phpunit-agent-reporter)
[![Total Downloads](https://poser.pugx.org/ergebnis/phpunit-agent-reporter/downloads)](https://packagist.org/packages/ergebnis/phpunit-agent-reporter)
[![Monthly Downloads](https://poser.pugx.org/ergebnis/phpunit-agent-reporter/d/monthly)](https://packagist.org/packages/ergebnis/phpunit-agent-reporter)

This project provides a [`composer`](https://getcomposer.org) package and a [Phar archive](https://www.php.net/manual/en/book.phar.php) with an extension for reporting [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit) test execution details to agents.

## Example

After installing and bootstrapping the extension, when running your tests with `phpunit/phpunit`, the extension will detect whether an agent is running the tests and replace the progress and result output of `phpunit/phpunit` with the compact output that `phpunit/phpunit` prints with the `--compact` option since [`phpunit/phpunit:13.2.0`](https://github.com/sebastianbergmann/phpunit/tree/13.2.0). The extension prints this output on every supported version of `phpunit/phpunit`.

When tests pass, the extension outputs:

```text
PHPUnit 13.4.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.26
Configuration: /path/to/phpunit.xml

OK (5 tests, 5 assertions)
```

When tests fail, the extension prints each failure as soon as it happens, followed by the summary:

```text
PHPUnit 13.4.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.26
Configuration: /path/to/phpunit.xml


--- FAILURE: Namespace\ExampleTest::testFailing
Failed asserting that false is true.

/path/to/ExampleTest.php:27

--- FAILURE: Namespace\ExampleTest::testComparisonFailing
Failed asserting that two strings are identical.
--- Expected
+++ Actual
@@ @@
-'foo'
+'bar'

/path/to/ExampleTest.php:35

FAILURES (5 tests, 5 assertions, 2 failures)
```

When tests error, the extension prints each error as soon as it happens, including previous exceptions:

```text
--- ERROR: Namespace\ExampleTest::testErroring
RuntimeException: Something went wrong.

/path/to/ExampleTest.php:27
Caused by
LogicException: Something else went wrong before.

/path/to/ExampleTest.php:30

ERRORS (5 tests, 4 assertions, 1 error)
```

The summary always counts deprecations, notices, warnings, skipped, incomplete, and risky tests. As `phpunit/phpunit` does, the extension prints details about deprecations, notices, warnings, skipped, and incomplete tests only when you enable them, for example with `--display-all-issues`, `--display-deprecations`, or the corresponding `displayDetailsOn*` attributes in `phpunit.xml`:

```text
OK (5 tests, 5 assertions, 1 deprecation)

--- DEPRECATION: /path/to/Example.php:42
Method Namespace\Example::doSomething() is deprecated.
Triggered by: Namespace\ExampleTest::testTriggeringDeprecation (/path/to/ExampleTest.php:21)
```

### Output format

The output follows the compact output of [`phpunit/phpunit:^13.4.0`](https://github.com/sebastianbergmann/phpunit/tree/13.4.0) and is meant for agents. The console output is not covered by the backward compatibility promise of this project: it may change in any release, for example when `phpunit/phpunit` changes its compact output.

When you run `phpunit/phpunit:^13.2.0` with the `--compact` option or the `PHPUNIT_COMPACT_OUTPUT` environment variable, `phpunit/phpunit` prints compact output itself, and the extension does nothing.

On older versions of `phpunit/phpunit`, the extension prints only what the events of that version provide:

- Before [`phpunit/phpunit:12.2.0`](https://github.com/sebastianbergmann/phpunit/tree/12.2.0), the extension prints a failed assertion in `setUpBeforeClass()` or `tearDownAfterClass()` as a failure, but the summary counts it as an error.
- Before [`phpunit/phpunit:12.1.0`](https://github.com/sebastianbergmann/phpunit/tree/12.1.0), the summary does not count notices triggered by `phpunit/phpunit` itself.
- Before [`phpunit/phpunit:11.5.51`](https://github.com/sebastianbergmann/phpunit/tree/11.5.51), [`phpunit/phpunit:12.5.9`](https://github.com/sebastianbergmann/phpunit/tree/12.5.9), and [`phpunit/phpunit:13.0.0`](https://github.com/sebastianbergmann/phpunit/tree/13.0.0), the summary does not count tests skipped because their test suite was skipped.
- Before [`phpunit/phpunit:13.2.0`](https://github.com/sebastianbergmann/phpunit/tree/13.2.0), the extension does not print deprecations, notices, warnings, and errors triggered outside of tests.

The extension cannot prevent `phpunit/phpunit` from printing output of tests, so for output printed by a test, the extension prints only the `--- OUTPUT:` header, and `phpunit/phpunit` prints the output right below it. This differs from compact output in the following ways:

- Trailing blank lines in the output remain.
- Before [`phpunit/phpunit:12.5.36`](https://github.com/sebastianbergmann/phpunit/tree/12.5.36) and [`phpunit/phpunit:13.3.5`](https://github.com/sebastianbergmann/phpunit/tree/13.3.5), `phpunit/phpunit` does not escape control characters in the output.
- With `--disallow-test-output`, `phpunit/phpunit` still prints the output.

For the same reason, `phpunit/phpunit` prints the message that the time limit for the test run was exceeded itself.

Because `phpunit/phpunit` may still report warnings after all tests have run, the extension prints the summary when the application has finished, that is, after a code coverage report printed to the console.

### Agent Detection

The extension uses [`ergebnis/agent-detector`](https://github.com/ergebnis/agent-detector) to detect the presence of agents.

The extension does not replace the default output when [`infection/infection`](https://github.com/infection/infection) runs tests against a mutant, because `infection/infection` relies on the default output of `phpunit/phpunit` to decide whether a mutant escaped.

## Compatibility

The extension is compatible with the following versions of `phpunit/phpunit`:

- [`phpunit/phpunit:^13.0.0`](https://github.com/sebastianbergmann/phpunit/tree/13.0.0)
- [`phpunit/phpunit:^12.5.8`](https://github.com/sebastianbergmann/phpunit/tree/12.5.8)
- [`phpunit/phpunit:^11.5.50`](https://github.com/sebastianbergmann/phpunit/tree/11.5.50)
- [`phpunit/phpunit:^10.5.62`](https://github.com/sebastianbergmann/phpunit/tree/10.5.62)

## Installation

### Installation with `composer`

Run

```sh
composer require --dev ergebnis/phpunit-agent-reporter
```

to install `ergebnis/phpunit-agent-reporter` as a `composer` package.

### Installation as Phar

Download `phpunit-agent-reporter.phar` from the [latest release](https://github.com/ergebnis/phpunit-agent-reporter/releases/latest).

## Usage

### Bootstrapping the extension

Before the extension can report test execution details in `phpunit/phpunit`, you need to bootstrap it.

### Bootstrapping the extension as a `composer` package

To bootstrap the extension as a `composer` package when using

- `phpunit/phpunit:^13.0.0`
- `phpunit/phpunit:^12.5.8`
- `phpunit/phpunit:^11.5.50`
- `phpunit/phpunit:^10.5.62`

adjust your `phpunit.xml` configuration file and configure the

- [`extensions` element](https://docs.phpunit.de/en/13.0/configuration.html#the-extensions-element) on [`phpunit/phpunit:^13.0.0`](https://docs.phpunit.de/en/13.0/)
- [`extensions` element](https://docs.phpunit.de/en/12.5/configuration.html#the-extensions-element) on [`phpunit/phpunit:^12.5.8`](https://docs.phpunit.de/en/12.5/)
- [`extensions` element](https://docs.phpunit.de/en/11.5/configuration.html#the-extensions-element) on [`phpunit/phpunit:^11.5.50`](https://docs.phpunit.de/en/11.5/)
- [`extensions` element](https://docs.phpunit.de/en/10.5/configuration.html#the-extensions-element) on [`phpunit/phpunit:^10.5.62`](https://docs.phpunit.de/en/10.5/)

```diff
 <phpunit
     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
     bootstrap="vendor/autoload.php"
 >
+    <extensions>
+        <bootstrap class="Ergebnis\PHPUnit\AgentReporter\Extension"/>
+    </extensions>
     <testsuites>
         <testsuite name="unit">
             <directory>test/Unit/</directory>
         </testsuite>
     </testsuites>
 </phpunit>
```

### Bootstrapping the extension as a PHAR

To bootstrap the extension as a PHAR when using

- `phpunit/phpunit:^13.0.0`
- `phpunit/phpunit:^12.5.8`
- `phpunit/phpunit:^11.5.50`
- `phpunit/phpunit:^10.5.62`

adjust your `phpunit.xml` configuration file and configure the

- [`extensionsDirectory` attribute](https://docs.phpunit.de/en/13.0/configuration.html#the-extensionsdirectory-attribute) and the [`extensions` element](https://docs.phpunit.de/en/13.0/configuration.html#the-extensions-element) on [`phpunit/phpunit:^13.0.0`](https://docs.phpunit.de/en/13.0/)
- [`extensionsDirectory` attribute](https://docs.phpunit.de/en/12.5/configuration.html#the-extensionsdirectory-attribute) and the [`extensions` element](https://docs.phpunit.de/en/12.5/configuration.html#the-extensions-element) on [`phpunit/phpunit:^12.5.8`](https://docs.phpunit.de/en/12.5/)
- [`extensionsDirectory` attribute](https://docs.phpunit.de/en/11.5/configuration.html#the-extensionsdirectory-attribute) and the [`extensions` element](https://docs.phpunit.de/en/11.5/configuration.html#the-extensions-element) on [`phpunit/phpunit:^11.5.50`](https://docs.phpunit.de/en/11.5/)
- [`extensionsDirectory` attribute](https://docs.phpunit.de/en/10.5/configuration.html#the-extensionsdirectory-attribute) and the [`extensions` element](https://docs.phpunit.de/en/10.5/configuration.html#the-extensions-element) on [`phpunit/phpunit:^10.5.62`](https://docs.phpunit.de/en/10.5/)

```diff
 <phpunit
     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
     bootstrap="vendor/autoload.php"
+    extensionsDirectory="directory/where/you/saved/the/extension/phars"
 >
+    <extensions>
+        <bootstrap class="Ergebnis\PHPUnit\AgentReporter\Extension"/>
+    </extensions>
     <testsuites>
         <testsuite name="unit">
             <directory>test/Unit/</directory>
         </testsuite>
     </testsuites>
 </phpunit>
```

## Changelog

The maintainers of this project record notable changes to this project in a [changelog](CHANGELOG.md).

## Contributing

The maintainers of this project suggest following the [contribution guide](.github/CONTRIBUTING.md).

## Code of Conduct

The maintainers of this project ask contributors to follow the [code of conduct](.github/CODE_OF_CONDUCT.md).

## General Support Policy

The maintainers of this project provide limited support.

## PHP Version Support Policy

This project currently supports the following PHP versions:

- [PHP 8.1](https://www.php.net/releases/#8.1.0) (has reached its end of life on December 31, 2025)
- [PHP 8.2](https://www.php.net/releases/#8.2.0)
- [PHP 8.3](https://www.php.net/releases/#8.3.0)
- [PHP 8.4](https://www.php.net/releases/#8.4.0)
- [PHP 8.5](https://www.php.net/releases/#8.5.0)

The maintainers of this project add support for a PHP version following its initial release and _may_ drop support for a PHP version when it has reached its [end of life](https://www.php.net/supported-versions.php).

## Security Policy

This project has a [security policy](.github/SECURITY.md).

## License

This project uses the [MIT license](LICENSE.md).

## Credits

This package is inspired by [`nunomaduro/pao`](https://github.com/nunomaduro/pao), originally licensed under MIT by [Nuno Maduro](https://github.com/nunomaduro).

The output of this package is inspired by and attempts to match the compact output of [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit) by [Sebastian Bergmann](https://github.com/sebastianbergmann), which `phpunit/phpunit` prints since [`phpunit/phpunit:13.2.0`](https://github.com/sebastianbergmann/phpunit/tree/13.2.0). This package backports it to older versions of `phpunit/phpunit`.

The sanitization of control characters in `src/Output/Sanitizer.php` is ported from [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit), originally licensed under BSD-3-Clause by [Sebastian Bergmann](https://github.com/sebastianbergmann).

## Social

Follow [@localheinz](https://x.com/intent/follow?screen_name=localheinz) and [@ergebnis](https://x.com/intent/follow?screen_name=ergebnis) on X.
