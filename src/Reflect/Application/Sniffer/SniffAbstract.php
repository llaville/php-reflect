<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Sniffer;

use Bartlett\Reflect;

use PhpParser\NodeVisitorAbstract;

/**
 * Base code for each sniff used to detect PHP features.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
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
    public function setUpBeforeSniff(): void
    {
        $this->visitor->getSubject()->dispatch(
            Reflect\Events::SNIFF,
            [
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
                'sniff'  => get_class($this),
            ]
        );
    }

    public function enterSniff(): void
    {
        $this->visitor->getSubject()->dispatch(
            Reflect\Events::SNIFF,
            [
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
                'sniff'  => get_class($this),
            ]
        );
    }

    public function leaveSniff(): void
    {
        $this->visitor->getSubject()->dispatch(
            Reflect\Events::SNIFF,
            [
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
                'sniff'  => get_class($this),
            ]
        );
    }

    public function tearDownAfterSniff(): void
    {
        $this->visitor->getSubject()->dispatch(
            Reflect\Events::SNIFF,
            [
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
                'sniff'  => get_class($this),
            ]
        );
    }

    public function setVisitor($visitor): void
    {
        $this->visitor = $visitor;
    }

    protected function getCurrentSpot($node): array
    {
        return [
            'file'    => realpath($this->visitor->getCurrentFile()),
            'line'    => $node->getLine()
        ];
    }

    protected function getCurrentSeverity(string $version, string $operator = 'lt', string $severity = 'error'): string
    {
        if (version_compare(PHP_VERSION, $version, $operator)) {
            return 'warning';
        }
        return $severity;
    }
}
