# phpunit-agent-reporter

[![Integrate](https://github.com/ergebnis/phpunit-agent-reporter/workflows/Integrate/badge.svg)](https://github.com/ergebnis/phpunit-agent-reporter/actions)
[![Merge](https://github.com/ergebnis/phpunit-agent-reporter/workflows/Merge/badge.svg)](https://github.com/ergebnis/phpunit-agent-reporter/actions)
[![Release](https://github.com/ergebnis/phpunit-agent-reporter/workflows/Release/badge.svg)](https://github.com/ergebnis/phpunit-agent-reporter/actions)
[![Renew](https://github.com/ergebnis/phpunit-agent-reporter/workflows/Renew/badge.svg)](https://github.com/ergebnis/phpunit-agent-reporter/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/phpunit-agent-reporter/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/phpunit-agent-reporter)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/phpunit-agent-reporter/v/stable)](https://packagist.org/packages/ergebnis/phpunit-agent-reporter)
[![Total Downloads](https://poser.pugx.org/ergebnis/phpunit-agent-reporter/downloads)](https://packagist.org/packages/ergebnis/phpunit-agent-reporter)
[![Monthly Downloads](http://poser.pugx.org/ergebnis/phpunit-agent-reporter/d/monthly)](https://packagist.org/packages/ergebnis/phpunit-agent-reporter)

This project provides a [`composer`](https://getcomposer.org) package and a [Phar archive](https://www.php.net/manual/en/book.phar.php) with an extension for reporting [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit) test execution details to agents.

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
- `phpunit/phpunit:^12.0.0`
- `phpunit/phpunit:^11.0.0`
- `phpunit/phpunit:^10.0.0`

adjust your `phpunit.xml` configuration file and configure the

- [`extensions` element](https://docs.phpunit.de/en/13.0/configuration.html#the-extensions-element) on [`phpunit/phpunit:^13.0.0`](https://docs.phpunit.de/en/13.0/)
- [`extensions` element](https://docs.phpunit.de/en/12.0/configuration.html#the-extensions-element) on [`phpunit/phpunit:^12.0.0`](https://docs.phpunit.de/en/12.0/)
- [`extensions` element](https://docs.phpunit.de/en/11.0/configuration.html#the-extensions-element) on [`phpunit/phpunit:^11.0.0`](https://docs.phpunit.de/en/11.0/)
- [`extensions` element](https://docs.phpunit.de/en/10.5/configuration.html#the-extensions-element) on [`phpunit/phpunit:^10.0.0`](https://docs.phpunit.de/en/10.5/)

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
- `phpunit/phpunit:^12.0.0`
- `phpunit/phpunit:^11.0.0`
- `phpunit/phpunit:^10.0.0`

adjust your `phpunit.xml` configuration file and configure the

- [`extensionsDirectory` attribute](https://docs.phpunit.de/en/13.0/configuration.html#the-extensionsdirectory-attribute) and the [`extensions` element](https://docs.phpunit.de/en/13.0/configuration.html#the-extensions-element) on [`phpunit/phpunit:^13.0.0`](https://docs.phpunit.de/en/13.0/)
- [`extensionsDirectory` attribute](https://docs.phpunit.de/en/12.0/configuration.html#the-extensionsdirectory-attribute) and the [`extensions` element](https://docs.phpunit.de/en/12.0/configuration.html#the-extensions-element) on [`phpunit/phpunit:^12.0.0`](https://docs.phpunit.de/en/12.0/)
- [`extensionsDirectory` attribute](https://docs.phpunit.de/en/11.0/configuration.html#the-extensionsdirectory-attribute) and the [`extensions` element](https://docs.phpunit.de/en/11.0/configuration.html#the-extensions-element) on [`phpunit/phpunit:^11.0.0`](https://docs.phpunit.de/en/11.0/)
- [`extensionsDirectory` attribute](https://docs.phpunit.de/en/10.5/configuration.html#the-extensionsdirectory-attribute) and the [`extensions` element](https://docs.phpunit.de/en/10.5/configuration.html#the-extensions-element) on [`phpunit/phpunit:^10.0.0`](https://docs.phpunit.de/en/10.5/)

```diff
 <phpunit
     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
     bootstrap="vendor/autoload.php"
+    extensionsDirectory="directory/where/you/saved/the/extension/phars"
 >
+    <extensions>
+        <extension class="Ergebnis\PHPUnit\AgentReporter\Extension"/>
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

You can support the maintenance of this project by [sponsoring @ergebnis](https://github.com/sponsors/ergebnis).

## PHP Version Support Policy

This project supports PHP versions with [active and security support](https://www.php.net/supported-versions.php).

The maintainers of this project add support for a PHP version following its initial release and drop support for a PHP version when it has reached the end of security support.

## Security Policy

This project has a [security policy](.github/SECURITY.md).

## License

This project uses the [MIT license](LICENSE.md).

## Credits

This package is inspired by [`nunomaduro/pao`](https://github.com/nunomaduro/pao), originally licensed under MIT by [Nuno Maduro](https://github.com/nunomaduro).

## Social

Follow [@localheinz](https://twitter.com/intent/follow?screen_name=localheinz) and [@ergebnis](https://twitter.com/intent/follow?screen_name=ergebnis) on Twitter.
