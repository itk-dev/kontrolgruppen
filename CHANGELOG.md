<!-- markdownlint-disable MD024 -->

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic
Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.0.0] - 2023-11-08

### Changed

- PHP Upgraded from 7.4 -> 8.2
- Symfony upgraded from 4.4 -> 6.3
- Design updates and bug fixes
- Implementation of Datafordeleren
- Implementation of BBR (Dataforsyningen & OIS)
- Borgerservice has been phased out

#### Hotfix - 2023-11-16

- SAML Fixed after Symfony upgrade

## [2.0.3] - 2023-04-13

### Changed

- Removed case create button from process index page

## [2.0.2] - 2023-02-27

### Changed

- Fixed check for company

## [2.0.1] - 2023-02-27

### Changed

- Upgraded to LexikFormFilterBundle v7
- Removed use of undefined method `Kontrolgruppen\CoreBundle\Entity\Process::getClient()`.

## [2.0.0] - 2023-02-21

### Changed

- Added core bundle to `core/` directory

## [1.12.2] - 2021-12-14

### Changed

- Required Core Bundle version 1.12.2.

## [1.12.0] - 2021-09-14

### Changed

- Required Core Bundle version 1.12.0.

## [1.11.5] - 2021-06-11

### Changed

- Required Core Bundle version 1.11.5.

## [1.11.4] - 2021-06-04

### Changed

- Required Core Bundle version 1.11.3.

## [1.11.3] - 2021-04-07

### Changed

- Required Core Bundle version 1.11.3.

## [1.11.2] - 2021-03-23

### Changed

- Required Core Bundle version 1.11.2.

## [1.11.1] - 2021-03-22

### Changed

- Required Core Bundle version 1.11.1.

## [1.11.0] - 2021-02-13

### Changed

- Required Core Bundle version 1.11.0.

### Fixed

- [KON-436](https://jira.itkdev.dk/browse/KON-436): Removing failing slack
  notification from Jenkinsfile.

## [1.10.0] - 2021-02-15

### Changed

- Required Core Bundle version 1.10.0.

### Added

- [SUPP0RT-62](https://jira.itkdev.dk/browse/SUPP0RT-62): Added documentation on
  fixtures and docker image.
- [SUPP0RT-62](https://jira.itkdev.dk/browse/SUPP0RT-62): Added missing
  environment variables

## [1.9.0] - 2021-01-04

### Changed

- Required version 1.9.0 of core bundle.

## [1.8.0] - 2020-11-19

### Changed

- Required version 1.8.0 of core bundle.

## [1.7.5] - 2020-11-16

### Changed

- Required version 1.7.5 of core bundle.

## [1.7.4] - 2020-10-29

### Changed

- Required version 1.7.4 of core bundle.

## [1.7.3] - 2020-10-21

### Changed

- Disabled Serviceplatformen. Enabled internal CPR service.

## [1.7.2] - 2020-10-01

### Changed

- Required version 1.7.2 of core bundle.

## [1.7.1] - 2020-09-17

### Changed

- [KON-395](https://jira.itkdev.dk/browse/KON-395): Required version 1.7.1 of
  core bundle
- [KON-396](https://jira.itkdev.dk/browse/KON-396): Feedback changes

## [1.7.0] - 2020-08-03

### Added

- [KON-288](https://jira.itkdev.dk/browse/KON-288): Added links between
  processes
- [KON-356](https://jira.itkdev.dk/browse/KON-356): Added weekly choice when
  entering future savings revenue entries

### Changed

- Required version 1.6.9 of bundle

## [1.6.9] - 2020-06-30

### Changed

- Required version 1.6.9 of bundle (hotfix)

## [1.6.8] - 2020-06-26

### Added

- [KON-330](https://jira.itkdev.dk/browse/KON-330): Mark statuses for use when
  completing processes
- [KON-364](https://jira.itkdev.dk/browse/KON-364): Preventing double
  submissions when creating new journal entry

## [1.6.7] - 2020-05-20

### Changed

- Required version 1.6.7 of bundle.

## [1.6.5] - 2020-05-20

### Changed

- Required version 1.6.6 of bundle.

## [1.6.4] - 2020-05-19

### Added

- Added CHANGELOG file
- [KON-289](https://jira.itkdev.dk/browse/KON-289): Show name of user when
  hovering AZ

### Changed

- [KON-361](https://github.com/aakb/kontrolgruppen/pull/81): Changed datepicker

[Unreleased]: https://github.com/itk-dev/kontrolgruppen/compare/2.0.3...HEAD
[3.0.0]: https://github.com/itk-dev/kontrolgruppen/compare/2.0.3...3.0.0
[2.0.3]: https://github.com/itk-dev/kontrolgruppen/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/itk-dev/kontrolgruppen/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/itk-dev/kontrolgruppen/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/itk-dev/kontrolgruppen/compare/1.12.2...2.0.0
[1.12.2]: https://github.com/itk-dev/kontrolgruppen/compare/1.12.0...1.12.2
[1.12.0]: https://github.com/itk-dev/kontrolgruppen/compare/1.11.5...1.12.0
[1.11.5]: https://github.com/itk-dev/kontrolgruppen/compare/.1.11.4..1.11.5
[1.11.4]: https://github.com/itk-dev/kontrolgruppen/compare/1.11.3...1.11.4
[1.11.3]: https://github.com/itk-dev/kontrolgruppen/compare/1.11.2...1.11.3
[1.11.2]: https://github.com/itk-dev/kontrolgruppen/compare/1.11.2...1.11.2
[1.11.1]: https://github.com/itk-dev/kontrolgruppen/compare/1.11.0...1.11.1
[1.11.0]: https://github.com/itk-dev/kontrolgruppen/compare/1.10.1...1.11.0
[1.10.0]: https://github.com/itk-dev/kontrolgruppen/compare/1.9.0...1.10.1
[1.9.0]: https://github.com/itk-dev/kontrolgruppen/compare/1.8.0...1.9.0
[1.8.0]: https://github.com/itk-dev/kontrolgruppen/compare/1.7.5...1.8.0
[1.7.5]: https://github.com/itk-dev/kontrolgruppen/compare/1.7.4...1.7.5
[1.7.4]: https://github.com/itk-dev/kontrolgruppen/compare/1.7.3...1.7.4
[1.7.3]: https://github.com/itk-dev/kontrolgruppen/compare/1.7.2...1.7.3
[1.7.2]: https://github.com/itk-dev/kontrolgruppen/compare/1.7.1...1.7.2
[1.7.1]: https://github.com/itk-dev/kontrolgruppen/compare/1.7.0...1.7.1
[1.7.0]: https://github.com/itk-dev/kontrolgruppen/compare/1.6.9...1.7.0
[1.6.9]: https://github.com/itk-dev/kontrolgruppen/compare/1.6.8...1.6.9
[1.6.8]: https://github.com/itk-dev/kontrolgruppen/compare/1.6.7...1.6.8
[1.6.7]: https://github.com/itk-dev/kontrolgruppen/compare/1.6.5...1.6.7
[1.6.5]: https://github.com/itk-dev/kontrolgruppen/compare/1.6.4...1.6.5
[1.6.4]: https://github.com/itk-dev/kontrolgruppen/releases/tag/1.6.4
