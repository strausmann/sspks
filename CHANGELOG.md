# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added

* GitPod Prebuilt [@strausmann](https://github.com/strausmann)

### Changed

* xdebug support für PHP 8 [@strausmann](https://github.com/strausmann)
* Upgrade to xdebug 3.1.1
* Update .dockerignore to cleanup the Docker Image
* Dockerfile base image to alpine:3.15 (PHP 7.4)
* Dockerfile Composer to v2

### Fixed

* Building GitPod Image failes, xdebug path incompatible with the new gitpod image based in PHP 8
* xdebug not working [@strausmann](https://github.com/strausmann)

## [1.1.6] - 2021-07-19	
### Added

* Support for DSM 7
* Support for locales
* Add GitPod Support [@strausmann](https://github.com/strausmann)
* Update with all new models [@strausmann](https://github.com/strausmann)

### Changed

* Description for Docker ENV added to the `README.md`
* Update with all new models
* Added new x86_64 avoton models (#60) [@eburud](https://github.com/eburud)

## [1.1.3] - 2018-07-25
### Added

* Add missing support_url and auto_upgrade_from fields [@MartinRothschink](https://github.com/MartinRothschink) (PR #55)
* Add more envirinment variables to override configuration (for use with docker)

### Changed

* Upgrade Docker to jdel/alpine:3.8, php7, composer 1.6.5, remove supervisor, change volumes to `/packages` and `/cache`
* Update `README` with docker instructions
* Update synology models (#50) [@4sag](https://github.com/4sag)


## [1.1.2] - 2018-01-19
### Added

* Implement qflags logic + some scrutinizer bits

## [1.1.1] - 2017-08-15
### Added

* Add version info from env variables (#41)

Override from ENV variables:

  - site.name (SSPKS_SITE_NAME)
  - site.theme (SSPKS_SITE_THEME)
  - site.redirectindex (SSPKS_SITE_REDIRECTINDEX)
  
And please Scrutinizer.
### Changed

* Minor update mostly useful for Docker.


## [1.1.0] - 2017-05-15
### Added

* A License
* Automated builds, tests and ci
* Facelift with Material Design

### Changed

* Updated models
* Extracted files are put in a cache/ dir
* Locales support for packages display name and description (#47)
* Locales in pkg name & description JSON result
* Fixed tests
* Scrutinizer didn't like string interpolation
* JsonOutput $langue default to 'enu'
* Docker image

## [1.0.0] - 2017-04-04
### Added

* Make Scrutinizer happy.

## [0.2.0] - 2017-01-16
### Changed

* Fix Dockerfile

## [0.1.0] - 2017-01-16
### Changed

* Fix Dockerfile

## [0.0.1] - 2013-04-12
### Added

* Initial version. Base functionality is there. 
* Requires more work on HTML5/CSS3 design.