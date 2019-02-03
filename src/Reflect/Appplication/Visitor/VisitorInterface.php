<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Visitor;

/**
 * Interface that all analysers using sniffs must implement.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
interface VisitorInterface
{
    public function setUpBeforeVisitor(): void;
    public function tearDownAfterVisitor(): void;
}
