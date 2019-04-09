<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Sniffer;

use PhpParser\NodeVisitor;

/**
 * Interface that all sniffs must implement.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
interface SniffInterface extends NodeVisitor
{
    // inherit NodeVisitor interface
    // ---
    // public function beforeTraverse(array $nodes);
    // public function enterNode(Node $node);
    // public function leaveNode(Node $node);
    // public function afterTraverse(array $nodes);

    public function setUpBeforeSniff(): void;
    public function enterSniff(): void ;
    public function leaveSniff(): void ;
    public function tearDownAfterSniff(): void ;
    public function setVisitor($visitor): void;
}
