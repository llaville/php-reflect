<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Analyser;

use Bartlett\Reflect;
use Bartlett\Reflect\Application\Events;

use PhpParser\Node;
use PhpParser\NodeVisitor;

/**
 * Base class to all analysers accessible through the AnalyserPlugin.
 * Provides common metrics for all analysers.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
abstract class AbstractAnalyser implements AnalyserInterface, NodeVisitor
{
    protected $namespaces = [];
    protected $testClass;
    protected $tokens;
    protected $file;
    protected $metrics = [];
    protected $subject;

    public function getSubject(): Reflect
    {
        return $this->subject;
    }

    public function getCurrentFile(): string
    {
        return $this->file;
    }

    public function getTokens(): array
    {
        return $this->tokens;
    }

    public function setSubject(Reflect $reflect): void
    {
        $this->subject = $reflect;
    }

    public function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }

    public function setCurrentFile(string $path): void
    {
        $this->file = $path;
    }

    public function getMetrics(): array
    {
        return [get_class($this) => $this->metrics];
    }

    public function getName(): string
    {
        $parts = explode('\\', get_class($this));
        return array_pop($parts);
    }

    public function getNamespace(): string
    {
        return implode('\\', array_slice(explode('\\', get_class($this)), 0, -1));
    }

    public function getShortName(): string
    {
        return strtolower(str_replace('Analyser', '', $this->getName()));
    }

    public function beforeTraverse(array $nodes)
    {
        $this->subject->dispatch(
            Events::BUILD,
            [
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
            ]
        );
    }

    public function enterNode(Node $node)
    {
        $this->subject->dispatch(
            Events::BUILD,
            [
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => $node,
            ]
        );
    }

    public function leaveNode(Node $node)
    {
        $this->subject->dispatch(
            Events::BUILD,
            [
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => $node,
            ]
        );
    }

    public function afterTraverse(array $nodes)
    {
        $this->subject->dispatch(
            Events::BUILD,
            [
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
            ]
        );
    }

    /**
     * Visits a namespace node.
     *
     * @param Node\Stmt\Namespace_ $namespace Represents a namespace in the data source
     *
     * @return void
     */
    protected function visitNamespace(Node\Stmt\Namespace_ $namespace): void
    {
        $this->namespaces[] = $namespace->name;
    }

    /**
     * Visits a class node.
     *
     * @param Node\Stmt\Class_ $class Represents a class in the namespace
     *
     * @return void
     */
    protected function visitClass(Node\Stmt\Class_ $class): void
    {
        $this->testClass = false;

        $parent = $class->extends;

        if (empty($parent)) {
            // No ancestry
            // Treat the class as a test case class if the name
            // of the parent class ends with "TestCase".

            if (substr((string) $class->name, -8) == 'TestCase') {
                $this->testClass = true;
            }
        } else {
            // Ancestry
            // Treat the class as a test case class if the name
            // of the parent class equals to "PHPUnit_Framework_TestCase".

            if ((string) $parent === 'PHPUnit_Framework_TestCase') {
                $this->testClass = true;
            }
        }
    }

    /**
     * Checks if a property is implicitly public (PHP 4 syntax)
     *
     * @param array              $tokens
     * @param Node\Stmt\Property $prop
     *
     * @return bool
     */
    protected function isImplicitlyPublicProperty(array $tokens, Node\Stmt\Property $prop): bool
    {
        $i = $prop->getAttribute('startTokenPos');
        return (isset($tokens[$i]) && $tokens[$i][0] == T_VAR);
    }

    /**
     * Checks if a method is implicitly public (PHP 4 syntax)
     *
     * @param array                 $tokens
     * @param Node\Stmt\ClassMethod $method
     *
     * @return bool
     */
    protected function isImplicitlyPublicFunction(array $tokens, Node\Stmt\ClassMethod $method): bool
    {
        $i = $method->getAttribute('startTokenPos');
        for ($c = count($tokens); $i < $c; ++$i) {
            $t = $tokens[$i];
            if ($t[0] == T_PUBLIC || $t[0] == T_PROTECTED || $t[0] == T_PRIVATE) {
                return false;
            }
            if ($t[0] == T_FUNCTION) {
                break;
            }
        }
        return true;
    }

    /**
     * Checks if array syntax is normal or short (PHP 5.4+ feature)
     *
     * @param array            $tokens
     * @param Node\Expr\Array_ $array
     *
     * @return bool
     */
    protected function isShortArraySyntax(array $tokens, Node\Expr\Array_ $array): bool
    {
        $i = $array->getAttribute('startTokenPos');
        return is_string($tokens[$i]);
    }
}
