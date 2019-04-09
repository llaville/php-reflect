<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class PluginListCommand
{
    private $baseDir;
    private $vendorDir;

    private $configFile;
    private $withoutPlugins;

    public function __construct(string $vendorDir, string $baseDir, string $configFile, bool $withoutPlugins)
    {
        $this->vendorDir = $vendorDir;
        $this->baseDir = $baseDir;

        $this->configFile = $configFile;

        $this->withoutPlugins = $withoutPlugins;
    }

    public function withPlugins(): bool
    {
        return !$this->withoutPlugins;
    }

    public function getConfigFilename()
    {
        return $this->configFile;
    }
}
