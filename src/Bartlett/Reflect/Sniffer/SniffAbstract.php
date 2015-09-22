<?php
/**
 * Base class to all sniffs.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Sniffer;

use Bartlett\Reflect;

use PhpParser\NodeVisitorAbstract;

/**
 * Base code for each sniff used to detect PHP features.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 4.0.0
 */
abstract class SniffAbstract extends NodeVisitorAbstract implements SniffInterface
{
    protected $visitor;

    // NodeVisitorAbstract inheritance
    // public function beforeTraverse(array $nodes)    { }
    // public function enterNode(Node $node) { }
    // public function leaveNode(Node $node) { }
    // public function afterTraverse(array $nodes)     { }

    // SniffInterface implements
    public function setUpBeforeSniff()
    {
        $this->visitor->getSubject()->dispatch(
            Reflect\Events::SNIFF,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
                'sniff'  => get_class($this),
            )
        );
    }

    public function enterSniff()
    {
        $this->visitor->getSubject()->dispatch(
            Reflect\Events::SNIFF,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
                'sniff'  => get_class($this),
            )
        );
    }

    public function leaveSniff()
    {
        $this->visitor->getSubject()->dispatch(
            Reflect\Events::SNIFF,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
                'sniff'  => get_class($this),
            )
        );
    }

    public function tearDownAfterSniff()
    {
        $this->visitor->getSubject()->dispatch(
            Reflect\Events::SNIFF,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
                'sniff'  => get_class($this),
            )
        );
    }

    public function setVisitor($visitor)
    {
        $this->visitor = $visitor;
    }

    protected function getCurrentSpot($node)
    {
        return array(
            'file'    => realpath($this->visitor->getCurrentFile()),
            'line'    => $node->getLine()
        );
    }
}
