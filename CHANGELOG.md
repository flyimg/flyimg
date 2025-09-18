# Flyimg Changelog

# [1.8.0](https://github.com/flyimg/flyimg/compare/1.7.11...1.8.0) (2025-09-18)


### Bug Fixes

* correct PHPDoc formatting ([e271b70](https://github.com/flyimg/flyimg/commit/e271b70b42ab77e471c663dac366218fe62f5a17))


### Features

* add support for POST uploads and S3 sources ([e27d180](https://github.com/flyimg/flyimg/commit/e27d18084ed4a6c035e7c212d8523a3d938f7788))

## [1.7.11](https://github.com/flyimg/flyimg/compare/1.7.10...1.7.11) (2025-09-14)


### Bug Fixes

* add the possiblity to enable or disable the default demo page ([5edeba6](https://github.com/flyimg/flyimg/commit/5edeba65706b2fcd723f611ff1572c28845fa282))

## [1.7.10](https://github.com/flyimg/flyimg/compare/1.7.9...1.7.10) (2025-09-09)


### Bug Fixes

* remove unnecessary whitespace in ImageHandler and improve exception message for temporary file access ([0028dc8](https://github.com/flyimg/flyimg/commit/0028dc8b2b9e60363e982af000e5e1c6b9e15b81))

## [1.7.9](https://github.com/flyimg/flyimg/compare/1.7.8...1.7.9) (2025-09-09)


### Bug Fixes

* update cleanup-tmp.sh to delete only files older than 1 hour and add retry mechanism in ImageHandler for temporary file access ([e626d85](https://github.com/flyimg/flyimg/commit/e626d853eef3fad059683d4d41eef06498e14c1d))

## [1.7.8](https://github.com/flyimg/flyimg/compare/1.7.7...1.7.8) (2025-09-08)


### Bug Fixes

* fix restricted domains where source image url contain one slash ([9554892](https://github.com/flyimg/flyimg/commit/955489278739a4d8a4b4f37b8f04a1b4be33b158))

## [1.7.7](https://github.com/flyimg/flyimg/compare/1.7.6...1.7.7) (2025-09-07)


### Bug Fixes

* convert RGBA images to RGB before processing in SmartCrop ([e362835](https://github.com/flyimg/flyimg/commit/e36283570233b20fef435c0edc7c98532eda4f5d))

## [1.7.6](https://github.com/flyimg/flyimg/compare/1.7.5...1.7.6) (2025-09-05)


### Bug Fixes

* update error handling and security domain checks ([dca76f5](https://github.com/flyimg/flyimg/commit/dca76f5f6eacff7d0c187a6733c3130c56550d61))
* update exception message for domain restriction in SecurityHandler ([ff714be](https://github.com/flyimg/flyimg/commit/ff714be010a1bf604e51834b43be70a902f512fd))

## [1.7.5](https://github.com/flyimg/flyimg/compare/1.7.4...1.7.5) (2025-07-29)


### Bug Fixes

* add dynamic URL support and copy functionality to example code snippet ([efa6725](https://github.com/flyimg/flyimg/commit/efa6725288316695d10a500bd59f450ef1ffba78))

## [1.7.4](https://github.com/flyimg/flyimg/compare/1.7.3...1.7.4) (2025-07-04)


### Bug Fixes

* update base image to 1.8.1, enhance error message in InputImage, and add AVIF test cases ([4c713cd](https://github.com/flyimg/flyimg/commit/4c713cd6d88e7309add97e9291282879a303a191))

## [1.7.3](https://github.com/flyimg/flyimg/compare/1.7.2...1.7.3) (2025-04-11)


### Bug Fixes

* add density option for PDF processing and update documentation. ([ad3f172](https://github.com/flyimg/flyimg/commit/ad3f172e6bd163aeaa379d103c7f7de96fffbac1))

## [1.7.2](https://github.com/flyimg/flyimg/compare/1.7.1...1.7.2) (2025-01-31)


### Bug Fixes

* fix Gravity value is ignored and crop always uses West, closed [#561](https://github.com/flyimg/flyimg/issues/561) ([d49b9fd](https://github.com/flyimg/flyimg/commit/d49b9fda38794fbc9d7025930c98c9c46831c61e))

## [1.7.1](https://github.com/flyimg/flyimg/compare/1.7.0...1.7.1) (2024-12-19)


### Bug Fixes

* add documentation for Support for -m and -mt flags with Cwebp, close [#547](https://github.com/flyimg/flyimg/issues/547) ([77b7f02](https://github.com/flyimg/flyimg/commit/77b7f0278c53f2151049074ab425948ab12c9b75))
* add Support for -m and -mt flags with Cwebp , closes [#547](https://github.com/flyimg/flyimg/issues/547) ([29ea4a6](https://github.com/flyimg/flyimg/commit/29ea4a65c50794db82d00035d4b19d0c66635168))
* fix linting errors ([7f3fdf5](https://github.com/flyimg/flyimg/commit/7f3fdf504a92475f3e83160a346ad31032839775))

# [1.7.0](https://github.com/flyimg/flyimg/compare/1.6.2...1.7.0) (2024-12-02)


### Features

* move to AGPL-3.0 ([71ceebf](https://github.com/flyimg/flyimg/commit/71ceebf3a113e7278739d19748e1e2d57408c10b))

## [1.6.2](https://github.com/flyimg/flyimg/compare/1.6.1...1.6.2) (2024-11-24)


### Bug Fixes

* fix cannot write mode RGBA as JPEG for avif image ([caff525](https://github.com/flyimg/flyimg/commit/caff525f1c19eb8c92ab85e990aaa382d1f81743))
* refactor python code for the smartcrop and facedetect, remove unused features ([0049fbc](https://github.com/flyimg/flyimg/commit/0049fbcc83a42434676539e81dac1b09650c0405))

## [1.6.1](https://github.com/flyimg/flyimg/compare/1.6.0...1.6.1) (2024-11-20)


### Bug Fixes

* add more tests to cover AppException and InvalidArgumentException ([9015c52](https://github.com/flyimg/flyimg/commit/9015c523c42b5a2221782f5f21b3d099fae8caa3))
* add UnauthorizedExceptionTest , plus fixing linting errors ([78bd848](https://github.com/flyimg/flyimg/commit/78bd848b330580a1d801cafc5bc7e563923cd3a4))
* add UnitTests for the new Exceptions ([19dae6a](https://github.com/flyimg/flyimg/commit/19dae6a282d3f26d9853296a4ebb284a360b05ae))
* fix Github workflow, closes [#543](https://github.com/flyimg/flyimg/issues/543) ([4e11b25](https://github.com/flyimg/flyimg/commit/4e11b25af024e0436a0e0a2b21db542dc71e1c02))
* fix linting errors and failed unitTest ([187154b](https://github.com/flyimg/flyimg/commit/187154b4a08a1af4de365c4cb890e17bd7b1d153))
* fix linting errors and refactor /MockResponseCodeServer code ([329e2f6](https://github.com/flyimg/flyimg/commit/329e2f6cc6e7cbf89594c609b60f2a4105b98427))

# [1.6.0](https://github.com/flyimg/flyimg/compare/1.5.0...1.6.0) (2024-11-18)


### Bug Fixes

* add remote_addr to logging ([80cef3f](https://github.com/flyimg/flyimg/commit/80cef3f4ee8fb733dbb0afd2cf925f6a3fbb4f7d))
* add X-Real-IP header ([fdbbbfc](https://github.com/flyimg/flyimg/commit/fdbbbfc417a34b78efea77243efc9eb765c02853))
* fix composer.lock file ([fbac5e4](https://github.com/flyimg/flyimg/commit/fbac5e40a17cbf5147f8956cc0a9f41cd0af5136))
* fix linting errors ([246a0b8](https://github.com/flyimg/flyimg/commit/246a0b8505cfe9a73211203d3b8fc9e0d1f5a89e))
* fixing linting error, related to [#538](https://github.com/flyimg/flyimg/issues/538) ([4f3877f](https://github.com/flyimg/flyimg/commit/4f3877fa911cc79608c826e7fa8b9028b60e8932))
* use X-Forwarded-For to get real client ip ([18c57a7](https://github.com/flyimg/flyimg/commit/18c57a755a9283237f7a24aa1d0efe076ca7db13))


### Features

* add watermark feature and documentation ([d5dc6e5](https://github.com/flyimg/flyimg/commit/d5dc6e5e4b142f69905e9437a5b4c09dd80fe2a9))
* replace silex/silex with flyimg/silex adaptation, which updates symfony components to the latest stable version, fixes [#535](https://github.com/flyimg/flyimg/issues/535) ([86cb7e7](https://github.com/flyimg/flyimg/commit/86cb7e711bebc804f56377a93442ea0577c162f7))
* update Docker base image to use Nginx latest stable release ([a4d0873](https://github.com/flyimg/flyimg/commit/a4d0873a8cb7daa07b6d757325e6b4943fb310b5))

# [1.5.0](https://github.com/flyimg/flyimg/compare/1.4.15...1.5.0) (2024-10-24)


### Bug Fixes

* add S3 storage optional parameters, update docs ([930dfe5](https://github.com/flyimg/flyimg/commit/930dfe587d330e0e167f323f3f2cd5301b8ae120))
* fix linting ([3c1b53a](https://github.com/flyimg/flyimg/commit/3c1b53a5c1a8f2e35a6c3e7f5c6fe4092f96da29))
* fix linting errors ([e79ed9a](https://github.com/flyimg/flyimg/commit/e79ed9a02b440732864d4d183419652a6ad613ff))
* fix linting errors ([182a019](https://github.com/flyimg/flyimg/commit/182a019809f4a176456e33c1b8a3b5ff6e5d7263))
* fix linting errors ([5114989](https://github.com/flyimg/flyimg/commit/5114989c058abd16bf93fb49b6d60aa1fb5b85c2))
* fix linting errors and extract constants into a separate file ([dd24926](https://github.com/flyimg/flyimg/commit/dd24926d43aadee7192b05b112d89e00e34eba1b))
* fix linting errors for the s3 storage ([ef65ead](https://github.com/flyimg/flyimg/commit/ef65eadaed72317e3f4c5899a2134c85943a8e64))
* fix missing include statement ([e679f38](https://github.com/flyimg/flyimg/commit/e679f38e98874f583fc172d7123f322642a8a9cd))
* fixing linting errors ([b2c6fd9](https://github.com/flyimg/flyimg/commit/b2c6fd9634fecd7c35153a6c6bb28deda461962d))
* fixing linting errors ([feec80b](https://github.com/flyimg/flyimg/commit/feec80bffdf89754d27e30e371f58b6665e05581))
* update documentation regarding s3 storage, related to [#533](https://github.com/flyimg/flyimg/issues/533) ([28a31b9](https://github.com/flyimg/flyimg/commit/28a31b9b36b11219781b51d7c5e6484cc0003ee6))


### Features

* adapt monologo as logging output and add log_level option, update docs, related to [#530](https://github.com/flyimg/flyimg/issues/530) ([ce3536d](https://github.com/flyimg/flyimg/commit/ce3536d604e886765d889e00d3ad52197320f2fb))

## [1.4.15](https://github.com/flyimg/flyimg/compare/1.4.14...1.4.15) (2024-10-21)


### Bug Fixes

* fix incorrect version shown in footer, closes [#528](https://github.com/flyimg/flyimg/issues/528) ([011a017](https://github.com/flyimg/flyimg/commit/011a0170ae36e3d33bfaa5835f91fec46936a9fd))

## [1.4.14](https://github.com/flyimg/flyimg/compare/1.4.13...1.4.14) (2024-10-09)


### Bug Fixes

* fix Same image content returned for different image URLs when loading within short timeframe, closes [#522](https://github.com/flyimg/flyimg/issues/522) ([632d790](https://github.com/flyimg/flyimg/commit/632d790392ecbabbe8d0e90ef6a0d72806d751c6))
* fixing linting error ([35774da](https://github.com/flyimg/flyimg/commit/35774daffc30e9cad20481ccf65d34258ac264a0))

## [1.4.13](https://github.com/flyimg/flyimg/compare/1.4.12...1.4.13) (2024-09-26)


### Bug Fixes

* fixing Cropping Image Changes Transparent Background To Solid Color, related to [#514](https://github.com/flyimg/flyimg/issues/514) ([753df2b](https://github.com/flyimg/flyimg/commit/753df2b38b61b333998864f2ea013a6e6b9ff8c2))

## [1.4.12](https://github.com/flyimg/flyimg/compare/1.4.11...1.4.12) (2024-09-13)


### Bug Fixes

* fix the issue of tmp folder permission, closes [#506](https://github.com/flyimg/flyimg/issues/506) ([0ed831d](https://github.com/flyimg/flyimg/commit/0ed831d9f250593569ec243ccec2f518267d6f22))

## [1.4.11](https://github.com/flyimg/flyimg/compare/1.4.10...1.4.11) (2024-09-12)


### Bug Fixes

* fix CHANGELOG entries ([19e9104](https://github.com/flyimg/flyimg/commit/19e9104d1fb9428e764ef4438da586e8157818f1))
* fix Version and Changelog file ([be0bfeb](https://github.com/flyimg/flyimg/commit/be0bfeb2773371b119d57366fd91cfc9a61aba33))

##  [1.4.10][Cleanup] (2024-09-12)


## [1.4.9](https://github.com/flyimg/flyimg/compare/1.4.8...1.4.9) (2024-09-11)


### Bug Fixes

* add requirements.txt for the python scripts ([be130ac](https://github.com/flyimg/flyimg/commit/be130acb497843e2680b3e031763f4a527c1cc49))
* fix linting errors ([bab7f7b](https://github.com/flyimg/flyimg/commit/bab7f7bc0686dfc4aaee677d1cb6e66f3212eec0))
* fix smart crop feature, use new base image version ([dcc4034](https://github.com/flyimg/flyimg/commit/dcc4034936b2ea8188081874f486ffd27e06421d))
* fixing python linting error ([553cc62](https://github.com/flyimg/flyimg/commit/553cc6220c7c438c8776a8be4197d73ee5b62eb7))

## [1.4.8](https://github.com/flyimg/flyimg/compare/1.4.7...1.4.8) (2024-09-09)


### Bug Fixes

* fixing S3 not working, upgrading league/flysystem lib, closes[#493](https://github.com/flyimg/flyimg/issues/493) ([12cf7df](https://github.com/flyimg/flyimg/commit/12cf7df616425c3d6ce2d49e4c0951fe6e1f8137))

## [1.4.7](https://github.com/flyimg/flyimg/compare/1.4.6...1.4.7) (2024-08-30)


### Bug Fixes

* use new base image version (php 8.3 + updated libraries versions) ([d09b6bb](https://github.com/flyimg/flyimg/commit/d09b6bb8a4102168b6f48b00eac6847c7334c681))

## [1.4.6](https://github.com/flyimg/flyimg/compare/1.4.5...1.4.6) (2024-08-29)


### Bug Fixes

* add cleanup cron job to purge tmp folder ([03b46f9](https://github.com/flyimg/flyimg/commit/03b46f9ba76577ed52e64a5d28f470b605350ebe))
* add cleanup cronjob documentation ([087b075](https://github.com/flyimg/flyimg/commit/087b0753b193332194ebd5e788a0e512226c0067))
* add Cleanup cronjob to purge var/tmp folder ([1f44916](https://github.com/flyimg/flyimg/commit/1f44916442d41ff2842da8b466cd2bf4d9eb5b71))
* use latest base image that include cron service ([5bcfa99](https://github.com/flyimg/flyimg/commit/5bcfa99cd15c71f44e6b75dc22ef0e475df66747))

## [1.4.5](https://github.com/flyimg/flyimg/compare/1.4.4...1.4.5) (2024-08-27)


### Bug Fixes

* fixing linting issues ([dd82edb](https://github.com/flyimg/flyimg/commit/dd82edb35d6fd58e09722c538df2d0123a7d0240))
* Redirect URLs without protocol schema are not handled correctly, closes [#486](https://github.com/flyimg/flyimg/issues/486) ([11b785e](https://github.com/flyimg/flyimg/commit/11b785e784d4980997e86899d4f8bdafe3b767f5))

## [1.4.4](https://github.com/flyimg/flyimg/compare/1.4.3...1.4.4) (2024-08-26)


### Bug Fixes

* 483 ([0b8027f](https://github.com/flyimg/flyimg/commit/0b8027fbe63474addb7d390a75b8cd53789e108a))
* 483 ([f50895e](https://github.com/flyimg/flyimg/commit/f50895e25b4e305bee8bcebce37ff2f350cc3c89))

## [1.4.3](https://github.com/flyimg/flyimg/compare/1.4.2...1.4.3) (2024-05-28)


### Bug Fixes

* add css style to version element ([abc0e85](https://github.com/flyimg/flyimg/commit/abc0e855197f6dd33ef8f4ad755930109ee44fab))
* add current version to the home page ([106461f](https://github.com/flyimg/flyimg/commit/106461f4812a39e7ce70ede71ae94ccda2d240d0))
* add missing ambimax/semantic-release-composer library ([12f0640](https://github.com/flyimg/flyimg/commit/12f06403c2e703acae714e99b583e5f36e8cdaac))
* fixing linting issues ([a5423f5](https://github.com/flyimg/flyimg/commit/a5423f53adb510196cd46c913913e3aa157fb3c1))
* fixing the @iwavesmedia/semantic-release-composer lib ([8e2b55a](https://github.com/flyimg/flyimg/commit/8e2b55a9bb51c1e797f3b26cb5cd9d5ea3cc8d52))

## [1.4.2](https://github.com/flyimg/flyimg/compare/1.4.1...1.4.2) (2024-05-28)


### Bug Fixes

* fix default sperator in js code ([ae85361](https://github.com/flyimg/flyimg/commit/ae8536174db6ef353a0e84c057ea3cedc6e2482a))

## [1.4.1](https://github.com/flyimg/flyimg/compare/1.4.0...1.4.1) (2024-05-24)


### Bug Fixes

* add options_separator to js code ([28fb1a0](https://github.com/flyimg/flyimg/commit/28fb1a0ebe940e2ba57e73fb03179d7c5f456015))

# [1.4.0](https://github.com/flyimg/flyimg/compare/1.3.5...1.4.0) (2024-05-10)


### Bug Fixes

* Rename the build image to flyimg/flyimg, run composer update, remove linux/arm/v6 from target platform ([526b3a1](https://github.com/flyimg/flyimg/commit/526b3a10fc2ccd844fe96950e9266a73c132a638))


### Features

* Update README ([b71c09e](https://github.com/flyimg/flyimg/commit/b71c09e93b43306d7ec35e52a478fe4854daa842))

## [1.3.5](https://github.com/flyimg/flyimg/compare/1.3.4...1.3.5) (2024-05-09)


### Bug Fixes

* update flyimg base version to support multi arch build, fixing [#414](https://github.com/flyimg/flyimg/issues/414) ([74d6336](https://github.com/flyimg/flyimg/commit/74d63362b6788a688c6d62742b783324cb9ec7d4))

## [1.3.4](https://github.com/flyimg/flyimg/compare/1.3.3...1.3.4) (2024-02-27)


### Bug Fixes

* adding linux/arm64/v8 architecture ([53c4006](https://github.com/flyimg/flyimg/commit/53c400672269d6492b91c482438cf5da1f189226))

## [1.3.3](https://github.com/flyimg/flyimg/compare/1.3.2...1.3.3) (2024-02-26)


### Bug Fixes

* add Multi-platform image with GitHub Actions, [#414](https://github.com/flyimg/flyimg/issues/414) ([2ab0d07](https://github.com/flyimg/flyimg/commit/2ab0d078e3f8c8eedfb8af682d075bcd7526bb6a))
* fixing Create manifest list stage: ([91524d3](https://github.com/flyimg/flyimg/commit/91524d3276e06080af0cfea91a1737a4504bb706))
* fixing Github Action workflow for Multi-platform Docker image ([1d0e144](https://github.com/flyimg/flyimg/commit/1d0e1444c75917a0dc82523192d3d4ace077c028))
* fixing github workflow ([5aff78a](https://github.com/flyimg/flyimg/commit/5aff78a820025d5351a62090c1d85ef49b94e74b))
* fixing linting errors ([f414470](https://github.com/flyimg/flyimg/commit/f4144703141942e8eb39ebab316d53f0caafabf7))
* fixing missing PLATFORM_PAIR for the articat upload (Github Action CD) ([84b55ff](https://github.com/flyimg/flyimg/commit/84b55ffdb3cc5902208d968c6998cc6b2de48c14))
* fixing Multi-platform image with GitHub Actions ([ce62db0](https://github.com/flyimg/flyimg/commit/ce62db07607989d63af0e08dd4366b0a691b55b3))
* fixing the base image path ([954ff76](https://github.com/flyimg/flyimg/commit/954ff767a03fa48388ab8683d1d24418bb25db76))
* fixing the REGISTRY_IMAGE tags ([eb3d09b](https://github.com/flyimg/flyimg/commit/eb3d09b989a43330ce14741189a743b282b7c6c9))
* fixing typo in docker Create manifest list and push step ([5a7658b](https://github.com/flyimg/flyimg/commit/5a7658bc5cace8825be2b35f2f3de9b447ae183e))
* fixing workflow release issue ([7864655](https://github.com/flyimg/flyimg/commit/7864655f5bc9995796bad0d63cf1e590c2b93ad7))

## [1.3.2](https://github.com/flyimg/flyimg/compare/1.3.1...1.3.2) (2024-02-21)


### Bug Fixes

* fixing facedetect issue for AVIF format images ([543ef80](https://github.com/flyimg/flyimg/commit/543ef80344827dc4a3695aca974ca544826870ff))

## [1.3.1](https://github.com/flyimg/flyimg/compare/1.3.0...1.3.1) (2024-02-18)


### Bug Fixes

* Fix the default value for heic_speed to 8 and make it variable in the config file ([25e7cdd](https://github.com/flyimg/flyimg/commit/25e7cdda5b5df75d1fd9a6816cc764c9f4760693))
* fixing issue with s3 storage system, closing [#325](https://github.com/flyimg/flyimg/issues/325) ([bdb59a0](https://github.com/flyimg/flyimg/commit/bdb59a06ea82eee9c250e7c165e4ccf87b12d42d))

# [1.3.0](https://github.com/flyimg/flyimg/compare/1.2.9...1.3.0) (2023-12-11)


### Bug Fixes

* fix avif test case ([141a2c4](https://github.com/flyimg/flyimg/commit/141a2c49b78a78b5a6e540acb54f6dabb13c8b88))
* fixing the timeout issue when loading source image ([06c6746](https://github.com/flyimg/flyimg/commit/06c6746073be2bcdc5f7b528a64425222559c6f2))


### Features

* add AVIF support ([b56b758](https://github.com/flyimg/flyimg/commit/b56b7586edb7741ce3650f6d62c40dfece80d557))
* use latest docker base image to add AVIF support ([3f13454](https://github.com/flyimg/flyimg/commit/3f13454701d03033a2181b501ca88e5fce057cb1))

## [1.2.9](https://github.com/flyimg/flyimg/compare/1.2.8...1.2.9) (2023-11-29)


### Bug Fixes

* fix demo page ([ee858ec](https://github.com/flyimg/flyimg/commit/ee858ecfa90ff772f80b20fac73e307e465a6666))

## [1.2.8](https://github.com/flyimg/flyimg/compare/1.2.7...1.2.8) (2023-11-27)


### Bug Fixes

* fixing the demo page style ([2c93f15](https://github.com/flyimg/flyimg/commit/2c93f154f8411e6c6f4913304d0769dc0120f1ca))
* improve the demo page style and elements ([8da103f](https://github.com/flyimg/flyimg/commit/8da103f3fcbdf717f85d19506a52f51486d6d77a))

## [1.2.7](https://github.com/flyimg/flyimg/compare/1.2.6...1.2.7) (2023-11-26)


### Bug Fixes

* fix the demo page js error ([87ffe8c](https://github.com/flyimg/flyimg/commit/87ffe8c1041456c0aa49eb977b3dd6961fcd3395))

## [1.2.6](https://github.com/flyimg/flyimg/compare/1.2.5...1.2.6) (2023-11-26)


### Bug Fixes

* fix CD workflow command timeout issue ([7cdca15](https://github.com/flyimg/flyimg/commit/7cdca15540cfa74694ca5a6e27fb5dd6314e31f1))

## [1.2.5](https://github.com/flyimg/flyimg/compare/1.2.4...1.2.5) (2023-11-26)


### Bug Fixes

* add Content-Length header ([a569e3f](https://github.com/flyimg/flyimg/commit/a569e3f66a0b29953ffc695e9362a633136469c2))
* fix dev branch deployment step ([34b714e](https://github.com/flyimg/flyimg/commit/34b714e8fe7bef2ae38c635b6047e0497b76bdc9))

## [1.2.4](https://github.com/flyimg/flyimg/compare/1.2.3...1.2.4) (2023-11-19)


### Bug Fixes

* add dev deployment workflow ([b6d1436](https://github.com/flyimg/flyimg/commit/b6d143618cd1a145b0cef55edbffcbbd2ea04126))
* fixing the wrong definition for the pdf page number, add descriptions for the URL image options for the new Demo page ([8231c6d](https://github.com/flyimg/flyimg/commit/8231c6dfc24ae2711d6732a1b9604efa90b8adf7))

## [1.2.3](https://github.com/flyimg/flyimg/compare/1.2.2...1.2.3) (2023-10-19)


### Bug Fixes

* add disable cache option ([59600ae](https://github.com/flyimg/flyimg/commit/59600ae1c833e98bd87f5b8f6403965666857cdc))

## [1.2.2](https://github.com/flyimg/flyimg/compare/1.2.1...1.2.2) (2023-10-04)


### Bug Fixes

* fix missing composer install cmd ([d8a2073](https://github.com/flyimg/flyimg/commit/d8a207328bdfd881ee49e15252ea2622b934b4fc))

## [1.2.1](https://github.com/flyimg/flyimg/compare/1.2.0...1.2.1) (2023-10-02)


### Bug Fixes

* fix missing variable env. to get the next release version ([6fa51ce](https://github.com/flyimg/flyimg/commit/6fa51ce01e27dda258442b9acb179b8f956d7458))

## [1.2.0](https://github.com/flyimg/flyimg/compare/1.1.58...1.2.0) (2023-10-02)


### Features

* update code to work on php8 and imagemagick 7 ([86a1dc4](https://github.com/flyimg/flyimg/commit/86a1dc4602ad000d6b78cf939477010ca92a2854))

## [1.1.58](https://github.com/flyimg/flyimg/compare/1.1.57...1.1.58) (2023-09-29)

### Bug Fixes

* add set to add a  GitHub release ([511dea9](https://github.com/flyimg/flyimg/commit/511dea943cba6cfe8f5f4ae3d273f97174d43c5a))
* adding release-note publisher ([e18af03](https://github.com/flyimg/flyimg/commit/e18af03c0beb0bd5782e564f8faef035db345122))
* fix changelog file header ([055b7e8](https://github.com/flyimg/flyimg/commit/055b7e85d4003fbcb4519a71be32eed45b52270b))
* fix Changelog title ([2f15f35](https://github.com/flyimg/flyimg/commit/2f15f3584f1a921f5bdc192e2b15d7359da8ae14))

## [1.1.57](https://github.com/flyimg/flyimg/compare/1.1.56...1.1.57) (2023-09-29)

### Bug Fixes (2023-09-29)

* add missing .releaserc file ([b5b8cad](https://github.com/flyimg/flyimg/commit/b5b8cad74881a8278652ba027b085499a87e375e))
* add tagFormat option for semantic-release ([1cb796f](https://github.com/flyimg/flyimg/commit/1cb796f8bde355de3d96f186aa92300b71972ca4))
* update Github token variable enviroment, closes [#363](https://github.com/flyimg/flyimg/issues/363) ([8b8d10a](https://github.com/flyimg/flyimg/commit/8b8d10a82ad5724f46b142ed0a58134d6e45157f))
* use PAT in GitHub action cd workflow ([e7f4da0](https://github.com/flyimg/flyimg/commit/e7f4da0c1370300482c61799108179d107f78f45))
* use Semantic-release for releasing and tagging ([9befb19](https://github.com/flyimg/flyimg/commit/9befb19b77c41fafa2c0a75e84946df3b8c55e07))


## [1.1.22](https://github.com/flyimg/flyimg/tree/1.1.22) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.21...1.1.22)

**Implemented enhancements:**

- Fix Changlelog generator in GitHub action workflow, closes \#287 [\#296](https://github.com/flyimg/flyimg/pull/296) ([sadok-f](https://github.com/sadok-f))

## [1.1.21](https://github.com/flyimg/flyimg/tree/1.1.21) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.20...1.1.21)

**Implemented enhancements:**

- Fix Changlelog generator in GitHub action workflow, fixes issue\#287 [\#295](https://github.com/flyimg/flyimg/pull/295) ([sadok-f](https://github.com/sadok-f))
- Add new workflow for GitHub release action, closes \#287 [\#294](https://github.com/flyimg/flyimg/pull/294) ([sadok-f](https://github.com/sadok-f))

## [1.1.20](https://github.com/flyimg/flyimg/tree/1.1.20) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.19...1.1.20)

## [1.1.19](https://github.com/flyimg/flyimg/tree/1.1.19) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.18...1.1.19)

**Implemented enhancements:**

- create new workflow for GitHub release, closes \#287 [\#293](https://github.com/flyimg/flyimg/pull/293) ([sadok-f](https://github.com/sadok-f))

## [1.1.18](https://github.com/flyimg/flyimg/tree/1.1.18) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.17...1.1.18)

**Implemented enhancements:**

- Fix Changelog generator in Github Action [\#287](https://github.com/flyimg/flyimg/issues/287)
- Fixing changelog generator in GitHub actions, closes \#287 [\#292](https://github.com/flyimg/flyimg/pull/292) ([sadok-f](https://github.com/sadok-f))
- Fixing changelog generator in GitHub actions, closes \#287 [\#291](https://github.com/flyimg/flyimg/pull/291) ([sadok-f](https://github.com/sadok-f))
- Fixing changelog generator in GitHub actions, closes \#287 [\#290](https://github.com/flyimg/flyimg/pull/290) ([sadok-f](https://github.com/sadok-f))
- Fixing changelog generator step in GitHub action, fixes \#287 [\#289](https://github.com/flyimg/flyimg/pull/289) ([sadok-f](https://github.com/sadok-f))
- Fix Changelog generator action step, fixes \#287 [\#288](https://github.com/flyimg/flyimg/pull/288) ([sadok-f](https://github.com/sadok-f))

## [1.1.17](https://github.com/flyimg/flyimg/tree/1.1.17) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.16...1.1.17)

**Implemented enhancements:**

- Fix deployment step in GitHub action, closes \#280 [\#286](https://github.com/flyimg/flyimg/pull/286) ([sadok-f](https://github.com/sadok-f))

## [1.1.16](https://github.com/flyimg/flyimg/tree/1.1.16) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.15...1.1.16)

**Implemented enhancements:**

- Fix deployment action, closes \#280 [\#285](https://github.com/flyimg/flyimg/pull/285) ([sadok-f](https://github.com/sadok-f))

## [1.1.15](https://github.com/flyimg/flyimg/tree/1.1.15) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.14...1.1.15)

**Implemented enhancements:**

- Implement Continuous Deployment \(CD\) for flyimg [\#280](https://github.com/flyimg/flyimg/issues/280)
- Fix deployment action, closes \#280 [\#284](https://github.com/flyimg/flyimg/pull/284) ([sadok-f](https://github.com/sadok-f))

## [1.1.14](https://github.com/flyimg/flyimg/tree/1.1.14) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.13...1.1.14)

**Implemented enhancements:**

- Add deploy to demo server as part of CD, closes \#280 [\#283](https://github.com/flyimg/flyimg/pull/283) ([sadok-f](https://github.com/sadok-f))

## [1.1.13](https://github.com/flyimg/flyimg/tree/1.1.13) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.12...1.1.13)

**Implemented enhancements:**

- Add code checkout step [\#282](https://github.com/flyimg/flyimg/pull/282) ([sadok-f](https://github.com/sadok-f))

## [1.1.12](https://github.com/flyimg/flyimg/tree/1.1.12) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.11...1.1.12)

**Implemented enhancements:**

- Add Docker build/push action when create new release, closes \#280 [\#281](https://github.com/flyimg/flyimg/pull/281) ([sadok-f](https://github.com/sadok-f))

## [1.1.11](https://github.com/flyimg/flyimg/tree/1.1.11) (2020-12-31)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.10...1.1.11)

**Implemented enhancements:**

- Replace Travis-ci by Github Actions [\#279](https://github.com/flyimg/flyimg/pull/279) ([sadok-f](https://github.com/sadok-f))

**Closed issues:**

- Travis-CI issue [\#257](https://github.com/flyimg/flyimg/issues/257)

## [1.1.10](https://github.com/flyimg/flyimg/tree/1.1.10) (2020-12-29)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.9...1.1.10)

**Implemented enhancements:**

- Move to main branch [\#277](https://github.com/flyimg/flyimg/issues/277)
- Update release branch to main [\#278](https://github.com/flyimg/flyimg/pull/278) ([sadok-f](https://github.com/sadok-f))

## [1.1.9](https://github.com/flyimg/flyimg/tree/1.1.9) (2020-12-28)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.8...1.1.9)

**Implemented enhancements:**

- Add Generate ChangeLog to GitHub action [\#275](https://github.com/flyimg/flyimg/pull/275) ([sadok-f](https://github.com/sadok-f))

## [1.1.8](https://github.com/flyimg/flyimg/tree/1.1.8) (2020-12-28)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.7...1.1.8)

**Implemented enhancements:**

- Update changelog [\#274](https://github.com/flyimg/flyimg/pull/274) ([sadok-f](https://github.com/sadok-f))

## [1.1.7](https://github.com/flyimg/flyimg/tree/1.1.7) (2020-12-28)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.6...1.1.7)

**Implemented enhancements:**

- Add Github Action to create new Tag/Release [\#269](https://github.com/flyimg/flyimg/issues/269)
- Add Codecov Report [\#267](https://github.com/flyimg/flyimg/issues/267)
- Downgrade Github action to v5 [\#272](https://github.com/flyimg/flyimg/pull/272) ([sadok-f](https://github.com/sadok-f))
- add empty tag\_prefix for Github actions [\#271](https://github.com/flyimg/flyimg/pull/271) ([sadok-f](https://github.com/sadok-f))
- Add new GitHub action to release tag closes \#269 [\#270](https://github.com/flyimg/flyimg/pull/270) ([sadok-f](https://github.com/sadok-f))
- Add CodeCov Repport, closes \#267 [\#268](https://github.com/flyimg/flyimg/pull/268) ([sadok-f](https://github.com/sadok-f))
- Add Last-Modified headers tag, closes \#261 [\#266](https://github.com/flyimg/flyimg/pull/266) ([sadok-f](https://github.com/sadok-f))

**Closed issues:**

- cache refresh [\#265](https://github.com/flyimg/flyimg/issues/265)
- question about query parameters [\#264](https://github.com/flyimg/flyimg/issues/264)
- Specify a cache validator [\#261](https://github.com/flyimg/flyimg/issues/261)

**Merged pull requests:**

- Bump symfony/http-kernel from 4.4.1 to 4.4.13 [\#262](https://github.com/flyimg/flyimg/pull/262) ([dependabot[bot]](https://github.com/apps/dependabot))
- Bump symfony/http-foundation from 4.4.1 to 4.4.7 [\#254](https://github.com/flyimg/flyimg/pull/254) ([dependabot[bot]](https://github.com/apps/dependabot))

## [1.1.6](https://github.com/flyimg/flyimg/tree/1.1.6) (2020-02-12)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.5...1.1.6)

**Implemented enhancements:**

- Update CHANGELOG.md file [\#251](https://github.com/flyimg/flyimg/issues/251)
- Update CHANGELOG file for the new release [\#249](https://github.com/flyimg/flyimg/issues/249)
- Question - I'm adding support for PDF's and Movies in a fork [\#241](https://github.com/flyimg/flyimg/issues/241)
- Update composer [\#239](https://github.com/flyimg/flyimg/issues/239)
- Update CHANGELOG file, closes \#251 [\#252](https://github.com/flyimg/flyimg/pull/252) ([sadok-f](https://github.com/sadok-f))
- Update changelog file closes \#249 [\#250](https://github.com/flyimg/flyimg/pull/250) ([sadok-f](https://github.com/sadok-f))
- Louisl/feature/movie support [\#246](https://github.com/flyimg/flyimg/pull/246) ([sadok-f](https://github.com/sadok-f))
- Updated readme for PDF and video [\#245](https://github.com/flyimg/flyimg/pull/245) ([louisl](https://github.com/louisl))
- Louisl/feature/movie support, closes \#243 [\#244](https://github.com/flyimg/flyimg/pull/244) ([sadok-f](https://github.com/sadok-f))
- Update composer, fixes \#239 [\#240](https://github.com/flyimg/flyimg/pull/240) ([sadok-f](https://github.com/sadok-f))

**Closed issues:**

- slash error  [\#235](https://github.com/flyimg/flyimg/issues/235)

**Merged pull requests:**

- Updated demo source links [\#247](https://github.com/flyimg/flyimg/pull/247) ([louisl](https://github.com/louisl))

## [1.1.5](https://github.com/flyimg/flyimg/tree/1.1.5) (2019-12-03)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.4...1.1.5)

**Implemented enhancements:**

- Update the dockerfile to use the latest base image 1.1.0 [\#229](https://github.com/flyimg/flyimg/issues/229)
- Update Readme: add Google Cloud Run description [\#227](https://github.com/flyimg/flyimg/issues/227)
- Change the default Nginx port to a variable environment  [\#225](https://github.com/flyimg/flyimg/issues/225)
- Add Cloud Run Button to Flyimg [\#223](https://github.com/flyimg/flyimg/issues/223)
- Refactor ExtractProcessor class [\#221](https://github.com/flyimg/flyimg/issues/221)
- Update CHANGELOG.md [\#219](https://github.com/flyimg/flyimg/issues/219)
- Fixing FaceDetect not working, closes \#236 [\#237](https://github.com/flyimg/flyimg/pull/237) ([sadok-f](https://github.com/sadok-f))
- Update Readme: add Google Cloud Run description, closes \#227 [\#233](https://github.com/flyimg/flyimg/pull/233) ([sadok-f](https://github.com/sadok-f))
- Use new deploy redirector [\#231](https://github.com/flyimg/flyimg/pull/231) ([jamesward](https://github.com/jamesward))
- Update the dockerfile to use the latest base image 1.1.0, closes \#229 [\#230](https://github.com/flyimg/flyimg/pull/230) ([sadok-f](https://github.com/sadok-f))
- Add Cloud Run Button to Flyimg [\#224](https://github.com/flyimg/flyimg/pull/224) ([sadok-f](https://github.com/sadok-f))
- Refactor ExtractProcessor class, closes \#221 [\#222](https://github.com/flyimg/flyimg/pull/222) ([sadok-f](https://github.com/sadok-f))
- Update CHANGELOG.md, closes \#219 [\#220](https://github.com/flyimg/flyimg/pull/220) ([sadok-f](https://github.com/sadok-f))

**Fixed bugs:**

- FaceDetect not working [\#236](https://github.com/flyimg/flyimg/issues/236)

**Merged pull requests:**

- Add changelogs for the new release [\#238](https://github.com/flyimg/flyimg/pull/238) ([sadok-f](https://github.com/sadok-f))
- Add ?git\_repo to cloud-run-button [\#232](https://github.com/flyimg/flyimg/pull/232) ([ahmetb](https://github.com/ahmetb))
- Update FUNDING.yml [\#228](https://github.com/flyimg/flyimg/pull/228) ([sadok-f](https://github.com/sadok-f))

## [1.1.4](https://github.com/flyimg/flyimg/tree/1.1.4) (2019-08-07)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.3...1.1.4)

**Implemented enhancements:**

- Update docker libraries and move to use tags images [\#217](https://github.com/flyimg/flyimg/issues/217)
- Update php minimum version to 7.1 [\#215](https://github.com/flyimg/flyimg/issues/215)
- Fix composer install warning [\#213](https://github.com/flyimg/flyimg/issues/213)
- How to specify a default domain name? [\#211](https://github.com/flyimg/flyimg/issues/211)
- Update the CHANGELOG.md [\#206](https://github.com/flyimg/flyimg/issues/206)
- Update docker libraries and move to use tags images, closes \#217 [\#218](https://github.com/flyimg/flyimg/pull/218) ([sadok-f](https://github.com/sadok-f))
- Update php minimum version to 7.1, closes \#215 [\#216](https://github.com/flyimg/flyimg/pull/216) ([sadok-f](https://github.com/sadok-f))
- Fix composer install warning, closes \#213 [\#214](https://github.com/flyimg/flyimg/pull/214) ([sadok-f](https://github.com/sadok-f))
- Instructions to replace default parameters file [\#212](https://github.com/flyimg/flyimg/pull/212) ([diego-vieira](https://github.com/diego-vieira))
- Update CHANGELOG file, closes \#206 [\#207](https://github.com/flyimg/flyimg/pull/207) ([sadok-f](https://github.com/sadok-f))

**Closed issues:**

- Update the sharpen and unsharp docs. [\#204](https://github.com/flyimg/flyimg/issues/204)

## [1.1.3](https://github.com/flyimg/flyimg/tree/1.1.3) (2019-01-09)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.2...1.1.3)

**Implemented enhancements:**

- Unsharp option doesn't work [\#202](https://github.com/flyimg/flyimg/issues/202)
- Missing User-Agent header to get images [\#200](https://github.com/flyimg/flyimg/issues/200)
- fix Unsharp option and add other options such as sharpen and blur [\#203](https://github.com/flyimg/flyimg/pull/203) ([sadok-f](https://github.com/sadok-f))
- New parameter to add more options to the header, fixes \#200 [\#201](https://github.com/flyimg/flyimg/pull/201) ([sadok-f](https://github.com/sadok-f))
- issue-\#197: Remove the Refresh option in the hash generated for output img [\#198](https://github.com/flyimg/flyimg/pull/198) ([sadok-f](https://github.com/sadok-f))
- issue-\#191: mage path parameters is ignored, closes \#191 [\#193](https://github.com/flyimg/flyimg/pull/193) ([sadok-f](https://github.com/sadok-f))
- issue-\#185: Fix missing demo images on Safari browser, closes \#185 [\#186](https://github.com/flyimg/flyimg/pull/186) ([sadok-f](https://github.com/sadok-f))
- issue-\#176: Update CodeClimate Test Reporter ID \#176 [\#183](https://github.com/flyimg/flyimg/pull/183) ([sadok-f](https://github.com/sadok-f))
- issue-\#176: Update CodeClimate Test Reporter ID \#176 [\#182](https://github.com/flyimg/flyimg/pull/182) ([sadok-f](https://github.com/sadok-f))
- issue-\#176: Update CodeClimate Test Reporter ID \#176 [\#181](https://github.com/flyimg/flyimg/pull/181) ([sadok-f](https://github.com/sadok-f))

**Fixed bugs:**

- Refresh option should be removed from the hashed string for the generated image [\#197](https://github.com/flyimg/flyimg/issues/197)
- A bug with fc and fb options [\#187](https://github.com/flyimg/flyimg/issues/187)
- issue-\#187: A bug with fc and fb options, closes \#187 [\#188](https://github.com/flyimg/flyimg/pull/188) ([sadok-f](https://github.com/sadok-f))

**Merged pull requests:**

- Updated the docs regarding blur, unsharp, and sharpen filters. Fixes \#204 [\#205](https://github.com/flyimg/flyimg/pull/205) ([baamenabar](https://github.com/baamenabar))

## [1.1.2](https://github.com/flyimg/flyimg/tree/1.1.2) (2018-08-03)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.1...1.1.2)

## [1.1.1](https://github.com/flyimg/flyimg/tree/1.1.1) (2017-11-06)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.1.0...1.1.1)

## [1.1.0](https://github.com/flyimg/flyimg/tree/1.1.0) (2017-10-29)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.0.1...1.1.0)

## [1.0.1](https://github.com/flyimg/flyimg/tree/1.0.1) (2017-10-25)

[Full Changelog](https://github.com/flyimg/flyimg/compare/1.0.0...1.0.1)

## [1.0.0](https://github.com/flyimg/flyimg/tree/1.0.0) (2017-06-30)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.2.0...1.0.0)

## [0.2.0](https://github.com/flyimg/flyimg/tree/0.2.0) (2017-06-30)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.1.9...0.2.0)

## [0.1.9](https://github.com/flyimg/flyimg/tree/0.1.9) (2017-06-30)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.1.8...0.1.9)

## [0.1.8](https://github.com/flyimg/flyimg/tree/0.1.8) (2017-06-27)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.1.7...0.1.8)

## [0.1.7](https://github.com/flyimg/flyimg/tree/0.1.7) (2017-06-21)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.1.6...0.1.7)

## [0.1.6](https://github.com/flyimg/flyimg/tree/0.1.6) (2017-03-28)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.1.5...0.1.6)

## [0.1.5](https://github.com/flyimg/flyimg/tree/0.1.5) (2017-03-08)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.1.4...0.1.5)

## [0.1.4](https://github.com/flyimg/flyimg/tree/0.1.4) (2017-03-07)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.1.3...0.1.4)

## [0.1.3](https://github.com/flyimg/flyimg/tree/0.1.3) (2017-02-18)

[Full Changelog](https://github.com/flyimg/flyimg/compare/0.1.2...0.1.3)

## [0.1.2](https://github.com/flyimg/flyimg/tree/0.1.2) (2017-02-09)

[Full Changelog](https://github.com/flyimg/flyimg/compare/b8b0881092b435f456f87f06d11a219810f897eb...0.1.2)



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
