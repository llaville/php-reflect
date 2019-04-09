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
class ReflectionClassCommand
{
    public $class;
    public $source;
    public $format;

    public function __construct(string $class, string $source, string $format)
    {
        $this->class  = $class;
        $this->source = $source;
        $this->format = $format;
    }
}
