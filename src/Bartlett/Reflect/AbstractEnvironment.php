<?php

namespace Bartlett\Reflect;

use JsonSchema\Validator;

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
     * Gets the name of json schema file that is used to validate
     * the json config file
     *
     * @return string
     */
    public function getJsonSchemaFilename()
    {
        return static::JSON_SCHEMA;
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
     * @return string JSON string if no error found
     *
     * @throws ParsingException  containing all details of JSON error syntax
     * @throws \RuntimeException if file not found or not readable
     */
    public function validateSyntax($file)
    {
        /**
         * This is a currently known PHP bug, but has not yet been fixed
         * @link http://bugs.php.net/bug.php?id=52769
         */
        $fname = realpath($file);
        if ($fname === false) {
            $fname = $file;
        }

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

    /**
     * Validates the schema of a JSON data structure according to
     * static::JSON_SCHEMA file rules
     *
     * @param string $data       The JSON data
     * @param string $schemaFile The JSON schema file
     *
     * @return void
     *
     * @throws ParsingException containing all errors that does not match json schema
     */
    public function validateSchema($data, $schemaFile)
    {
        $schemaData = $this->validateSyntax($schemaFile);

        $validator = new Validator();
        $validator->check(json_decode($data), json_decode($schemaData));

        if (!$validator->isValid()) {
            $errors = '"'
                . $this->getJsonFilename()
                . '" is invalid, the following errors were found :' . "\n";
            foreach ($validator->getErrors() as $error) {
                $errors .= sprintf("- [%s] %s\n", $error['property'], $error['message']);
            }
            throw new ParsingException($errors);
        }
    }
}
