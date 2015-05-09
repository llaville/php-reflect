<?php
/**
 * Prints the result of a TestRunner run using Monolog.
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    GIT: $Id$
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 3.1.0
 */

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FilterHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Prints the result of a TestRunner run using Monolog and few handlers.
 *
 * - We log all PHPUnit events to a local file "phpunit-phpreflect.log"
 *   and keep history 30 days
 * - We log some PHPUnit events, depending of --verbose, --debug and --colors switches,
 *   directly to the CLI console
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://php5.laurent-laville.org/reflect/
 */
class MonologConsoleLogger extends Logger
{
    /**
     * Console logger class constructor
     *
     * @param string $name  The logging channel
     * @param string $level The minimum logging level
     */
    public function __construct($name = 'YourLogger', $level = Logger::DEBUG)
    {
        $stream = new RotatingFileHandler(__DIR__ . '/phpunit-phpreflect-php' . PHP_VERSION_ID . '.log', 30);
        $stream->setFilenameFormat('{filename}-{date}', 'Ymd');

        $console = new StreamHandler('php://stdout');
        $console->setFormatter(new LineFormatter("%message%\n", null, true));

        $filter = new FilterHandler($console);

        $handlers = array($filter, $stream);

        parent::__construct($name, $handlers);
    }

    /**
     * Returns list of accepted log levels
     *
     * @return array
     */
    public function getAcceptedLevels()
    {
        $handlers = $this->getHandlers();
        foreach ($handlers as &$handler) {
            if ($handler instanceof FilterHandler) {
                return $handler->getAcceptedLevels();
            }
        }
        return array();
    }

    /**
     * Defines log levels that will be accepted.
     *
     * @param int|array $minLevelOrList A list of levels to accept or a minimum level if maxLevel is provided
     * @param int       $maxLevel       Maximum level to accept, only used if $minLevelOrList is not an array
     *
     * @return void
     */
    public function setAcceptedLevels($minLevelOrList = Logger::DEBUG, $maxLevel = Logger::EMERGENCY)
    {
        $handlers = $this->getHandlers();
        foreach ($handlers as &$handler) {
            if ($handler instanceof FilterHandler) {
                $handler->setAcceptedLevels($minLevelOrList, $maxLevel);
                break;
            }
        }
    }
}
