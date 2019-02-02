<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Appplication\Command;

use Bartlett\Reflect\Analyser\AnalyserManager;

use Symfony\Component\Finder\Finder;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
abstract class AnalyserBaseHandler
{
    /**
     * Registers all analysers
     * bundled with distribution and declared by user in the JSON config file.
     *
     * @return AnalyserManager
     */
    protected function registerAnalysers(): AnalyserManager
    {
        $namespaces = [];

        $am = new AnalyserManager($namespaces);
        $am->registerAnalysers();

        return $am;
    }

    protected function findProvider(string $source, string $alias = null): Finder
    {
        $finder = new Finder();
        $finder->files();
        $finder->in($source);

        return $finder;
    }
}
