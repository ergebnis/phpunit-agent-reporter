# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`1.0.0...main`][1.0.0...main].

### Added

- Added `phpunitDeprecations`, `phpunitNotices`, and `phpunitWarnings` categories reporting deprecations, notices, and warnings triggered by `phpunit/phpunit` itself ([#69]), by [@localheinz]

## [`1.0.0`][1.0.0]

For a full diff see [`0.3.0...1.0.0`][0.3.0...1.0.0].

### Changed

- Adjusted `Reporter\JsonReporter` to always include deprecation, incomplete test, notice, risky test, skipped test, and warning counts in the summary, and to report their details whenever they occur, regardless of `phpunit/phpunit` fail-on configuration ([#44]), by [@localheinz]

## [`0.3.0`][0.3.0]

For a full diff see [`0.2.0...0.3.0`][0.2.0...0.3.0].

### Changed

- Started using `ergebnis/agent-detector` to detect the presence of agents ([#11]), by [@localheinz]

## [`0.2.0`][0.2.0]

For a full diff see [`0.1.0...0.2.0`][0.1.0...0.2.0].

### Added

- Added total assertion count to report summary ([#4]), by [@localheinz]

## [`0.1.0`][0.1.0]

For a full diff see [`1902cc2...0.1.0`][1902cc2...0.1.0].

### Added

- Added `Agent\Detector` ([#1]), by [@localheinz]
- Added `Extension` ([#3]), by [@localheinz]

[0.1.0]: https://github.com/ergebnis/phpunit-agent-reporter/releases/tag/0.1.0
[0.2.0]: https://github.com/ergebnis/phpunit-agent-reporter/releases/tag/0.2.0
[0.3.0]: https://github.com/ergebnis/phpunit-agent-reporter/releases/tag/0.3.0
[1.0.0]: https://github.com/ergebnis/phpunit-agent-reporter/releases/tag/1.0.0

[0.1.0...0.2.0]: https://github.com/ergebnis/phpunit-agent-reporter/compare/0.1.0...0.2.0
[0.2.0...0.3.0]: https://github.com/ergebnis/phpunit-agent-reporter/compare/0.2.0...0.3.0
[0.3.0...1.0.0]: https://github.com/ergebnis/phpunit-agent-reporter/compare/0.3.0...1.0.0
[1.0.0...main]: https://github.com/ergebnis/phpunit-agent-reporter/compare/1.0.0...main
[1902cc2...0.1.0]: https://github.com/ergebnis/phpunit-agent-reporter/compare/1902cc2...0.1.0

[#1]: https://github.com/ergebnis/phpunit-agent-reporter/pull/1
[#3]: https://github.com/ergebnis/phpunit-agent-reporter/pull/3
[#4]: https://github.com/ergebnis/phpunit-agent-reporter/pull/4
[#11]: https://github.com/ergebnis/phpunit-agent-reporter/pull/11
[#44]: https://github.com/ergebnis/phpunit-agent-reporter/pull/44
[#69]: https://github.com/ergebnis/phpunit-agent-reporter/pull/69

[@localheinz]: https://github.com/localheinz
