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
use Bartlett\Reflect\Visitor\VisitorInterface;

use PhpParser\Node;
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
    /**
     * @return void
     */
    public function setUpBeforeSniff(): void
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

    /**
     * @return void
     */
    public function enterSniff(): void
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

    /**
     * @return void
     */
    public function leaveSniff(): void
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

    /**
     * @return void
     */
    public function tearDownAfterSniff(): void
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

    /**
     * @param VisitorInterface $visitor
     * @return void
     */
    public function setVisitor(VisitorInterface $visitor): void
    {
        $this->visitor = $visitor;
    }

    /**
     * @param Node $node
     * @return array
     *
     * @psalm-return array{file: false|string, line: mixed}
     */
    protected function getCurrentSpot(Node $node): array
    {
        return array(
            'file'    => realpath($this->visitor->getCurrentFile()),
            'line'    => $node->getLine()
        );
    }

    protected function getCurrentSeverity(string $version, string $operator = 'lt', string $severity = 'error'): string
    {
        if (version_compare(PHP_VERSION, $version, $operator)) {
            return 'warning';
        }
        return $severity;
    }
}
