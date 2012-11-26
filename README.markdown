PHP_Reflect
===========

**PHP_Reflect** is a library that
adds the ability to reverse-engineer classes, interfaces, functions, constants, namespaces and more.

Installation
------------

PHP_Reflect should be installed using the [PEAR Installer](http://pear.php.net/).
This installer is the backbone of PEAR, which provides a distribution system for PHP packages, 
and is shipped with every release of PHP since version 4.3.0.

The PEAR channel (`bartlett.laurent-laville.org`) that is used to distribute PHP_Reflect
needs to be registered with the local PEAR environment. 
Furthermore, component such as famous unit test framework PHPUnit is hosted
on the PHPUnit PEAR channel (`pear.phpunit.de`).

    $ pear channel-discover bartlett.laurent-laville.org
    Adding Channel "bartlett.laurent-laville.org" succeeded
    Discovery of channel "bartlett.laurent-laville.org" succeeded

    $ pear channel-discover pear.phpunit.de
    Adding Channel "pear.phpunit.de" succeeded
    Discovery of channel "pear.phpunit.de" succeeded
    
This has to be done only once. Now the PEAR Installer can be used to install packages from the Bartlett channel.

    $ pear install bartlett/PHP_Reflect
    downloading PHP_Reflect-1.5.0.tgz ...
    Starting to download PHP_Reflect-1.5.0.tgz (102,183 bytes)
    .....................done: 102,183 bytes
    upgrade ok: channel://bartlett.laurent-laville.org/PHP_Reflect-1.5.0   

After the installation you can find the PHP_Reflect source files inside your local PEAR directory.


Documentation
-------------

The documentation for PHP_Reflect is available in different formats:

* [English, multiple HTML files](http://php5.laurent-laville.org/reflect/manual/current/en/index.html)
* [English, single HTML file](http://php5.laurent-laville.org/reflect/manual/current/en/phpreflect-book.html)
* [English, PDF](http://php5.laurent-laville.org/reflect/manual/current/en/phpreflect-book.pdf)
* [English, CHM](http://php5.laurent-laville.org/reflect/manual/current/en/phpreflect-book.chm.zip)
* [English, EPUB](http://php5.laurent-laville.org/reflect/manual/current/en/phpreflect-book.epub)
