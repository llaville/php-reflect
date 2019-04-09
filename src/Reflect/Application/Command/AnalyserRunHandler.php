<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use Bartlett\Reflect;
use Bartlett\Reflect\Application\Command\AnalyserBaseHandler;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class AnalyserRunHandler extends AnalyserBaseHandler implements CommandHandlerInterface
{
    protected $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher, string $configFilename)
    {
        parent::__construct($configFilename);
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(AnalyserRunCommand $command): array
    {
        $source = $command->source;
        $analysers = $command->analysers;

        $finder = $this->findProvider($source, $alias = '');
        $dataSourceId = realpath($source);

        $reflect = new Reflect();
        $reflect->setEventDispatcher($this->eventDispatcher);
        $reflect->setDataSourceId($dataSourceId);

        $am = $this->registerAnalysers();

        $analysersAvailable = [];
        foreach ($am->getAnalysers() as $analyser) {
            $analysersAvailable[$analyser->getShortName()] = $analyser;
        }

        // attach valid analysers only
        foreach ($analysers as $analyserName) {
            if (!array_key_exists(strtolower($analyserName), $analysersAvailable)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '"%s" Analyser is not installed.',
                        $analyserName
                    )
                );
            }
            $reflect->addAnalyser($analysersAvailable[$analyserName]);
        }

        $pm = new Reflect\Plugin\PluginManager($this->eventDispatcher, $this->configFilename);
        if ($command->withPlugins()) {
            $pm->registerPlugins();
        }

        $response = $reflect->parse($finder);

        return $response;
    }
}
