<?php declare(strict_types=1);

/**
 * Common interface to all sniffers.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Sniffer;

use Bartlett\Reflect\Visitor\VisitorInterface;

use PhpParser\NodeVisitor;

/**
 * Interface that all sniffs must implement.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 4.0.0
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
    public function enterSniff(): void;
    public function leaveSniff(): void;
    public function tearDownAfterSniff(): void;
    public function setVisitor(VisitorInterface $visitor): void;
}
