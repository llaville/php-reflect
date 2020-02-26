# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/),
using the [Keep a CHANGELOG](http://keepachangelog.com) principles.

## [Unreleased]

## [4.3.1] - 2020-02-26

### Changed

- Add support to Symfony 4 components (Thanks to @remicollet for his PR to solve issue GH-36)

## [5.0.0-beta1] - 2019-04-13

### Changed

- unit tests and examples were fixed

## [5.0.0-alpha3] - 2019-04-07

### Changed

- remove extra uml writer commands

## [5.0.0-alpha2] - 2019-02-03

### Changed

- cache plugin feature was removed
- filter and transform final results features were removed since version 4.3
- diagram commands are only available on symfony console if `bartlett/umlwriter` package is installed
- diagnose command use `zendframework/zenddiagnostics` package
- console commands are now in `bartlett` namespace
- use command bus as new architecture
- remove extra/useless `Bartlett` directory level to match PSR-4

## [5.0.0-alpha1] - 2019-01-20

### Changed

- drop support to PHP 5. Requires at least PHP 7.1 or greater
- drop Growl notifier support
- update PHP-Parser to 4.0 for parsing code from PHP 5.2 to 7.3 on PHP
 platform 7.0 or better

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
