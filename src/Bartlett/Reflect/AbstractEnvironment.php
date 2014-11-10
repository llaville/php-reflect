<?php

namespace Bartlett\Reflect;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

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
abstract class AbstractEnvironment
{
    /**
     * Gets the name of json config file
     *
     * @return string
     */
    public function getJsonFilename()
    {
        return static::JSON_FILE;
    }

    /**
     * Gets the name of environment variable that identify
     * the json config file
     *
     * @return string
     */
    public function getEnv()
    {
        return static::ENV;
    }

    /**
     * Validates the syntax of a JSON file
     *
     * @param string $file The JSON file to check
     *
     * @return mixed JSON data if no error found
     *
     * @throws ParsingException  containing all details of JSON error syntax
     * @throws \RuntimeException if file not found or not readable
     */
    public function validateSyntax($file)
    {
        $fname = realpath($file);

        if (!file_exists($fname)) {
            throw new \RuntimeException('File "' . $file . '" not found.');
        }
        if (!is_readable($fname)) {
            throw new \RuntimeException('File "' . $file . '" is not readable.');
        }

        $json = file_get_contents($fname);

        $parser = new JsonParser();
        $result = $parser->lint($json);
        if (null === $result) {
            if (defined('JSON_ERROR_UTF8')
                && JSON_ERROR_UTF8 === json_last_error()
            ) {
                throw new ParsingException(
                    '"' . $file . '" is not UTF-8, could not parse as JSON'
                );
            }
            return $json;
        }
        throw $result;
    }
}
