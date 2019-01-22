<?php

declare(strict_types=1);

/**
 * Diagnoses the system to identify common errors.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Api;

/**
 * Diagnoses the system to identify common errors.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-RC1
 */
class Diagnose extends BaseApi
{
    /**
     * Diagnoses the system to identify common errors.
     *
     * @return array
     */
    public function run()
    {
        return $this->request('diagnose/run');
    }
}
