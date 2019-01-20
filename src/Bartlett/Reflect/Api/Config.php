<?php

declare(strict_types=1);

/**
 * Validates structure of the JSON configuration file.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Api;

/**
 * Validates structure of the JSON configuration file.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Config extends BaseApi
{
    /**
     * Validates a JSON configuration file.
     *
     * @param string $file Path to {json} file
     *
     * @return array json data found
     * @throws \RuntimeException                if configuration file
     *                                          does not exists or not readable
     * @throws \Seld\JsonLint\ParsingException  if configuration file is invalid format
     */
    public function validate($file)
    {
        return $this->request('config/validate', 'GET', array($file));
    }
}
