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
class ReflectionFunctionCommand
{
    public $function;
    public $source;
    public $format;

    public function __construct(string $function, string $source, string $format)
    {
        $this->function  = $function;
        $this->source = $source;
        $this->format = $format;
    }
}
