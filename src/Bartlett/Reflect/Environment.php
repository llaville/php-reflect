<?php

namespace Bartlett\Reflect;

/**
 * Application Environment.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.6.0
 */
class Environment extends AbstractEnvironment
{
    const JSON_FILE   = 'phpreflect.json';
    const JSON_SCHEMA = 'phpreflect-schema.json';
    const ENV         = 'REFLECT';
}
