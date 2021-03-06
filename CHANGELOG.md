# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/),
using the [Keep a CHANGELOG](http://keepachangelog.com) principles.

## [Unreleased]

## [4.4.x-dev]

Project reached End-Of-Life (2020-09-15).
No features will be accepted. Only bug and security fixes will be proceeded.

## [4.4.1] - 2020-10-06

### Fixed

- Allows installation with PHP 8.0
- Named parameters confusion with PHP 8 (thanks to @remicollet for his PR on CompatInfo)

## [4.4.0] - 2020-07-07

### Added

- Event classes `Bartlett\Reflect\Event\*Event` to follow principle of simpler event dispatching

### Changed

- Drop support to Symfony 2 and 3, and allows only Symfony 4 LTS and Symfony 5
- Drop support to PHP 5
- Drop support to `sebastian/version` v1
- Raise `phpdocumentor/reflection-docblock` constraint to v4 to allows only PHP 7 compatibility
- Raise `phpdocumentor/type-resolver` constraint to v0.4 or v1.0 (on recommendation of @remicollet)
- Raise `justinrainbow/json-schema` constraint to v5.2
- Raise `seld/jsonlint` constraint to v1.4
- Raise `nikic/php-parser` constraint to v4.5
- [Simpler event dispatching](https://symfony.com/blog/new-in-symfony-4-3-simpler-event-dispatching) is possible since Symfony 4.3
- Clean-up phpDoc tags
- Sets PHP minimum requirement to 7.1.3

### Removed

- Application Events class constants `Bartlett\Reflect\Events` (replaced by individual Event class. See simpler event dispatching)
- public method `Bartlett\Reflect\Console\Application::setDispatcher` was removed and replaced by private method `Bartlett\Reflect\Console\Application::initDispatcher`
  to avoid conflict between Symfony 4.4 and Symfony 5.x

### Fixed

- **CachePlugin** : if TEMP env var is not defined, fallback to [sys_get_temp_dir()](https://www.php.net/manual/en/function.sys-get-temp-dir.php)

## [4.3.1] - 2020-02-26

### Changed

- Add support to Symfony 4 components (Thanks to @remicollet for his PR to solve issue GH-36)

## [4.3.0] - 2018-11-25

### Changed

- update PHP-Parser to 3.1 for parsing code from PHP 5.2 to 7.2 on PHP
 platform 5.5 or better
- add support to Symfony Components 4.x

## [4.2.2] - 2017-12-14

### Fixed

- fix regression with previous cache configuration (thanks to Remi Collet)

## [4.2.1] - 2017-12-12

### Changed

- fix minimum requirements to PHP 5.5

## [4.2.0] - 2017-12-12

### Added

- show PHP-Parser errors, when found on php scripts

## [4.1.0] - 2017-04-10

- update to PHP-Parser 2.1

## [4.0.0] - 2015-12-04

- introduce sniff architecture, and drop support to PHP 5.3

[unreleased]: https://github.com/llaville/php-reflect/compare/4.4.0...HEAD
[4.4.0]: https://github.com/llaville/php-reflect/compare/4.3.1...4.4.0
[4.3.1]: https://github.com/llaville/php-reflect/compare/4.3.0...4.3.1
[4.3.0]: https://github.com/llaville/php-reflect/compare/4.2.2...4.3.0
[4.2.2]: https://github.com/llaville/php-reflect/compare/4.2.1...4.2.2
[4.2.1]: https://github.com/llaville/php-reflect/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/llaville/php-reflect/compare/4.2.1...4.2.0
[4.1.0]: https://github.com/llaville/php-reflect/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/llaville/php-reflect/compare/3.1.2...4.0.0
