<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use JsonSchema\Validator;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class ConfigValidateHandler implements CommandHandlerInterface
{
    public function __invoke(ConfigValidateCommand $command): array
    {
        return $this->getJsonConfigFile($command->file);
    }

    /**
     * Gets the contents of a JSON configuration file.
     *
     * @param string $file (optional) Path to a JSON file
     *
     * @return array
     * @throws \RuntimeException if configuration file
     *                           does not exists or not readable
     * @throws ParsingException  if configuration file is invalid format
     */
    protected function getJsonConfigFile($file): array
    {
        $json = $this->validateSyntax($file);

        $schemaFile = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/bin/'
            . basename($file) . '-schema';

        if (file_exists($schemaFile)) {
            // validate against schema only if available
            $this->validateSchema($json, $schemaFile, $file);
        }

        $var = json_decode($json, true);

        if (!is_array($var['source-providers'])) {
            $var['source-providers'] = array($var['source-providers']);
        }
        if (!is_array($var['analysers'])) {
            $var['analysers'] = array($var['analysers']);
        }
        if (!is_array($var['plugins'])) {
            $var['plugins'] = array($var['plugins']);
        }
        return $var;
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
    protected function validateSyntax($file): string
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
     * @param string $configFile The JSON config file
     *
     * @return void
     *
     * @throws ParsingException containing all errors that does not match json schema
     */
    protected function validateSchema($data, $schemaFile, $configFile): void
    {
        $schemaData = $this->validateSyntax($schemaFile);

        $validator = new Validator();
        $validator->check(json_decode($data), json_decode($schemaData));

        if (!$validator->isValid()) {
            $errors = '"' . $configFile . '" is invalid, '
                . 'the following errors were found :' . "\n";
            foreach ($validator->getErrors() as $error) {
                $errors .= sprintf("- [%s] %s\n", $error['property'], $error['message']);
            }
            throw new ParsingException($errors);
        }
    }
}
