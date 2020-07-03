<?php declare(strict_types=1);

/**
 * Base class to all sniffs.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Sniffer;

use Bartlett\Reflect\Event\SniffEvent;

use PhpParser\NodeVisitorAbstract;

/**
 * Base code for each sniff used to detect PHP features.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
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
            new SniffEvent(
                $this,
                array(
                    'method' => get_class($this) . '::' . __FUNCTION__,
                    'node'   => null,
                    'sniff'  => get_class($this),
                )
            )
        );
    }

    public function enterSniff()
    {
        $this->visitor->getSubject()->dispatch(
            new SniffEvent(
                $this,
                array(
                    'method' => get_class($this) . '::' . __FUNCTION__,
                    'node'   => null,
                    'sniff'  => get_class($this),
                )
            )
        );
    }

    public function leaveSniff()
    {
        $this->visitor->getSubject()->dispatch(
            new SniffEvent(
                $this,
                array(
                    'method' => get_class($this) . '::' . __FUNCTION__,
                    'node'   => null,
                    'sniff'  => get_class($this),
                )
            )
        );
    }

    public function tearDownAfterSniff()
    {
        $this->visitor->getSubject()->dispatch(
            new SniffEvent(
                $this,
                array(
                    'method' => get_class($this) . '::' . __FUNCTION__,
                    'node'   => null,
                    'sniff'  => get_class($this),
                )
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

    protected function getCurrentSeverity($version, $operator = 'lt', $severity = 'error')
    {
        if (version_compare(PHP_VERSION, $version, $operator)) {
            return 'warning';
        }
        return $severity;
    }
}
