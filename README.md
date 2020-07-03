[![Latest Stable Version](https://img.shields.io/packagist/v/bartlett/php-reflect)](https://packagist.org/packages/bartlett/php-reflect)
[![Minimum PHP Version)](https://img.shields.io/packagist/php-v/bartlett/php-reflect)](https://php.net/)

# PHP Reflect

**PHP Reflect** is a library that
adds the ability to reverse-engineer classes, interfaces, functions, constants, namespaces, traits and more.

Running on PHP greater than 7.1 for parsing source code in a format PHP 5.2 to PHP 7.4

## Requirements

* PHP 7.1.3 or greater
* PHPUnit 7 or greater (if you want to run unit tests)

## Installation

The recommended way to install this library is [through composer](http://getcomposer.org).
If you don't know yet what is composer, have a look [on introduction](http://getcomposer.org/doc/00-intro.md).

```bash
composer require bartlett/php-reflect
```

## Build PHAR distribution

To build PHAR distribution, you'll need to get a copy of this project https://github.com/humbug/box

**WARNING**: Don't forget to run following command (before compiling archive), if you want to have a PHAR manifest up-to-date !
```bash
php phar-manifest.php > manifest.txt
```

Run following command
```bash
box.phar compile
```

You should get output that look like
```
Box version 3.8.4@120b0a3 2019-12-13 17:22:43 UTC

 // Loading the configuration file "/shared/backups/bartlett/php-reflect/box.json.dist".

🔨  Building the PHAR "/shared/backups/bartlett/php-reflect/bin/phpreflect.phar"

? No compactor to register
? Adding main file: /shared/backups/bartlett/php-reflect/bin/phpreflect
? Adding requirements checker
? Adding binary files
    > No file found
? Auto-discover files? No
? Exclude dev files? No
? Adding files
    > 890 file(s)
? Using stub file: /shared/backups/bartlett/php-reflect/phar-stub.php
? Skipping dumping the Composer autoloader
? Removing the Composer dump artefacts
? Compressing with the algorithm "GZ"
    > Warning: the extension "zlib" will now be required to execute the PHAR
? Setting file permissions to 0755
* Done.

No recommendation found.
No warning found.

 // PHAR: 916 files (987.43KB)
 // You can inspect the generated PHAR with the "info" command.

 // Memory usage: 20.52MB (peak: 21.40MB), time: 1sec
```

## Documentation

The documentation for PHP Reflect 4.2 is available
in [English](http://php5.laurent-laville.org/reflect/manual/4.2/en/)
to read it online or download to read it later (multiple formats).

AsciiDoc source code are available on `docs` folder of the repository.

## Contributors

* Laurent Laville (Lead Dev)
* Thanks to Nikita Popov who wrote a marvellous [PHP Parser](https://github.com/nikic/PHP-Parser) and simplify the job of PHP Reflect.
* Thanks also to Remi Collet, a contributor of first hours.

[![](https://sourcerer.io/fame/llaville/llaville/php-reflect/images/0)](https://sourcerer.io/fame/llaville/llaville/php-reflect/links/0)
[![](https://sourcerer.io/fame/llaville/llaville/php-reflect/images/1)](https://sourcerer.io/fame/llaville/llaville/php-reflect/links/1)
[![](https://sourcerer.io/fame/llaville/llaville/php-reflect/images/2)](https://sourcerer.io/fame/llaville/llaville/php-reflect/links/2)
[![](https://sourcerer.io/fame/llaville/llaville/php-reflect/images/3)](https://sourcerer.io/fame/llaville/llaville/php-reflect/links/3)
[![](https://sourcerer.io/fame/llaville/llaville/php-reflect/images/4)](https://sourcerer.io/fame/llaville/llaville/php-reflect/links/4)
[![](https://sourcerer.io/fame/llaville/llaville/php-reflect/images/5)](https://sourcerer.io/fame/llaville/llaville/php-reflect/links/5)
[![](https://sourcerer.io/fame/llaville/llaville/php-reflect/images/6)](https://sourcerer.io/fame/llaville/llaville/php-reflect/links/6)
[![](https://sourcerer.io/fame/llaville/llaville/php-reflect/images/7)](https://sourcerer.io/fame/llaville/llaville/php-reflect/links/7)

## License

This project is licensed under the BSD-3-Clause License - see the [LICENSE](https://github.com/llaville/php-reflect/blob/master/LICENSE) file for details
