<?php declare(strict_types=1);

/**
 * Application Environment.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Application Environment.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since    Class available since Release 2.6.0
 */
class Environment
{
    protected static $container;

    /**
     * Search a json file on a list of scan directory pointed by
     * the BARTLETT_SCAN_DIR env var.
     * Config filename is identify by the BARTLETTRC env var.
     *
     * @return string|boolean FALSE if not found, otherwise its location
     */
    public static function getJsonConfigFilename()
    {
        $scanDir = getenv('BARTLETT_SCAN_DIR');
        if ($scanDir) {
            $dirs = explode(PATH_SEPARATOR, $scanDir);

            foreach ($dirs as $scanDir) {
                $filename = $scanDir . DIRECTORY_SEPARATOR . getenv('BARTLETTRC');
                if (file_exists($filename) && is_file($filename)) {
                    return realpath($filename);
                }
            }
        }
        return false;
    }

    /**
     * Defines the scan directories where to search for a json config file.
     *
     * @return void
     * @see    getJsonConfigFilename()
     */
    public static function setScanDir()
    {
        if (!getenv("BARTLETT_SCAN_DIR")) {
            $home = defined('PHP_WINDOWS_VERSION_BUILD') ? 'USERPROFILE' : 'HOME';
            $dirs = array(
                realpath('.'),
                getenv($home) . DIRECTORY_SEPARATOR . '.config',
                DIRECTORY_SEPARATOR . 'etc',
            );
            putenv("BARTLETT_SCAN_DIR=" . implode(PATH_SEPARATOR, $dirs));
        }
    }

    /**
     * Gets a client to interact with the API
     *
     * @return \Bartlett\Reflect\Client\ClientInterface
     */
    public static function getClient()
    {
        $prefix = str_replace(array('.json', '.dist'), '', getenv('BARTLETTRC'));
        return self::getContainer()->get($prefix . '.client');
    }

    /**
     * Gets a compatible PSR-3 logger
     *
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger()
    {
        $prefix = str_replace(array('.json', '.dist'), '', getenv('BARTLETTRC'));
        return self::getContainer()->get($prefix . '.logger');
    }

    private static function createContainer(): ContainerBuilder
    {
        // default values
        $clientClass = 'Bartlett\Reflect\Client\LocalClient';
        $loggerClass = 'Bartlett\Reflect\Plugin\Log\DefaultLogger';

        $container = new ContainerBuilder();

        $jsonFile = self::getJsonConfigFilename();
        if ($jsonFile) {
            $json = file_get_contents($jsonFile);
            $var  = json_decode($json, true);

            if (isset($var['services'])) {
                foreach ($var['services'] as $service) {
                    if (array_key_exists('client', $service)) {
                        if (class_exists($service['client'])) {
                            $clientClass = $service['client'];
                        }
                    }
                    if (array_key_exists('logger', $service)) {
                        if (class_exists($service['logger'])) {
                            $loggerClass = $service['logger'];
                        }
                    }
                }
            }
        }

        $prefix = str_replace(array('.json', '.dist'), '', getenv('BARTLETTRC'));

        // client for interacting with the API
        $container->register($prefix . '.client', $clientClass);

        // PSR-3 compatible logger
        $container->register($prefix . '.logger', $loggerClass);

        return $container;
    }

    private static function getContainer()
    {
        if (self::$container === null) {
            self::$container = self::createContainer();
        }
        return self::$container;
    }
}
