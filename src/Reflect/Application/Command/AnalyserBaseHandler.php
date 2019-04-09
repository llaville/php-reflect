<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use Bartlett\Reflect\Application\Analyser\AnalyserManager;
use Bartlett\Reflect\Application\Command\ConfigValidateCommand;
use Bartlett\Reflect\Application\Command\ConfigValidateHandler;

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
    protected $dataSourceId;
    protected $provider;
    protected $configFilename;

    public function __construct(string $configFilename)
    {
        $this->configFilename = $configFilename;
    }

    /**
     * Registers all analysers
     * bundled with distribution and declared by user in the JSON config file.
     *
     * @return AnalyserManager
     */
    protected function registerAnalysers(): AnalyserManager
    {
        $namespaces = [];

        $am = new AnalyserManager($namespaces, $this->configFilename);
        $am->registerAnalysers();

        return $am;
    }

    /**
     * Global identification of the data source
     *
     * @param string $source Path to the data source (dir, file, archive)
     * @param string $alias  Shortcut that referenced the data source
     *
     * @return Finder
     * @throws \RuntimeException
     */
    protected function findProvider(string $source, string $alias): Finder
    {
        if (!$alias) {
            $src = realpath($source);
            if (PATH_SEPARATOR == ';') {
                // on windows platform, remove the drive identifier
                $src = substr($src, 2);
            }

            if (is_dir($src)) {
                $provider = ['in' => $src];
            } elseif (is_file($src)) {
                $ext = pathinfo($src, PATHINFO_EXTENSION);

                if (in_array($ext, ['phar', 'zip', 'gz', 'tar', 'tgz', 'rar'])) {
                    // archive file
                    $provider = ['in' => 'phar://' . $src];
                } else {
                    $provider = ['in' => dirname($src), 'name' => basename($src)];
                    $this->dataSourceId = $src;
                }
            }
        }

        if (!isset($provider)) {
            // try to validate syntax and content of this json config file
            $command = new ConfigValidateCommand($this->configFilename);
            $configValidateHandler = new ConfigValidateHandler();
            $var = $configValidateHandler($command);

            foreach ($var['source-providers'] as $provider) {
                $in = trim($provider['in']);
                if (empty($in)) {
                    throw new \RuntimeException('key "in" is empty in source-providers section of configuration file');
                }
                $src = explode(' as ', $in);
                $src = array_map('trim', $src);

                if (!empty($alias) && count($src) < 2) {
                    // searching on alias, which is not provided
                    continue;
                }
                $i = empty($alias) ? 0 : 1;
                // search on data source path ($i = 0) or alias ($i = 1)
                if ($src[$i] == $source) {
                    $provider['in'] = $src[0];
                    break;
                }
                unset($provider);
            }

            if (!isset($provider)) {
                throw new \RuntimeException(
                    'None data source matching'
                );
            }
        }

        if (substr($provider['in'], 0, 1) == '.') {
            // relative local file
            $provider['in'] = realpath($provider['in']);
        }
        if (PATH_SEPARATOR == ';') {
            // normalizes path to unix format
            $provider['in'] = str_replace(DIRECTORY_SEPARATOR, '/', $provider['in']);
        }
        if (!isset($provider['name'])) {
            // default file extensions to scan
            $provider['name'] = '/\\.(php|inc|phtml)$/';
        }
        if (!isset($this->dataSourceId)) {
            $this->dataSourceId = $provider['in'];
        }

        $finder = new Finder();
        $finder->files();

        $constraints = [
            'in',                       // Location
            'exclude',                  // Exclude directories
            'name', 'notName',          // File name constraints
            'path', 'notPath',          // Path constraints
            'size',                     // File size constraints
            'date',                     // File date constraints
            'depth',                    // Directory depth constraints
            'contains', 'notContains',  // File contents constraints
        ];
        foreach ($constraints as $constraint) {
            if (isset($provider[$constraint])) {
                if (is_array($provider[$constraint])) {
                    $args = $provider[$constraint];
                } else {
                    $args = [$provider[$constraint]];
                }
                foreach ($args as $arg) {
                    $finder->{$constraint}($arg);
                }
            }
        }
        $this->provider = $provider;
        return $finder;
    }
}
