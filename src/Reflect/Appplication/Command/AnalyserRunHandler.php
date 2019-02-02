<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use Bartlett\Reflect;
use Bartlett\Reflect\Appplication\Command\AnalyserBaseHandler;

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
    public function __invoke(AnalyserRunCommand $command): array
    {
        $source = $command->source;
        $filter = $command->filter;
        $analysers = $command->analysers;

        $finder = $this->findProvider($source, $alias = null);
        $dataSourceId = realpath($source);

        if ($finder === false) {
            throw new \RuntimeException(
                'None data source matching'
            );
        }

        if ($filter === false) {
            // filter feature is not possible on reflection:* commands
            $filter = function ($data) {
                return $data;
            };
        }

        $reflect = new Reflect();
        //$reflect->setEventDispatcher($this->eventDispatcher);
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

        /*
        $pm = new PluginManager($this->eventDispatcher);
        if ($this->registerPlugins) {
            $pm->registerPlugins();
        }*/

        $response = $reflect->parse($finder);

        $response = $filter($response);

        if (!empty($format)) {
            $transformMethod = sprintf('transformTo%s', ucfirst($format));
            if (!method_exists($this, $transformMethod)) {
                throw new \InvalidArgumentException(
                    'Could not render result in this format (not implemented).'
                );
            }
            $response = $this->$transformMethod($response);
        }

        return $response;
    }
}
