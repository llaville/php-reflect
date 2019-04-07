<?php

declare(strict_types=1);

/**
 * Unit Test Case that covers each Model representative.
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 3.0.0-alpha2
 */

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect\Application\Analyser\ReflectionAnalyser;
use Bartlett\Reflect\Application\Command\AnalyserRunCommand;
use Bartlett\Reflect\Application\Command\AnalyserRunHandler;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Unit Test Case that covers Bartlett\Reflect\Model\*Model
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 */
abstract class GenericModelTest extends \PHPUnit\Framework\TestCase
{
    protected static $fixtures;
    protected static $fixture;
    protected static $models;
    protected static $api;

    /**
     * Sets up the shared fixture.
     *
     * @return void
     * @link   http://phpunit.de/manual/current/en/fixtures.html#fixtures.sharing-fixture
     */
    public static function setUpBeforeClass()
    {
        self::$fixtures = dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'fixtures' . DIRECTORY_SEPARATOR;

        self::$fixture = self::$fixtures . self::$fixture;

        $locator = new InMemoryLocator();
        $locator->addHandler(
            new AnalyserRunHandler(
                new EventDispatcher(),
                __DIR__ . '/../Environment/phpreflect.json'
            ),
            AnalyserRunCommand::class
        );

        $handlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            $locator,
            new InvokeInflector()
        );

        $commandBus = new CommandBus([$handlerMiddleware]);

        $analyserId   = ReflectionAnalyser::class;
        $dataSource   = self::$fixture;
        $analysers    = ['reflection'];

        $command = new AnalyserRunCommand($dataSource, $analysers, true);

        $metrics = $commandBus->handle($command);
        self::$models = $metrics[$analyserId];
    }
}
