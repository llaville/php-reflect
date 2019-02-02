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
class DiagramPackageCommand
{
    public $namespace;
    public $source;
    public $engine;

    public function __construct(string $namespace, string $source, string $engine)
    {
        $this->namespace  = $namespace;
        $this->source = $source;
        $this->engine = $engine;
    }
}
