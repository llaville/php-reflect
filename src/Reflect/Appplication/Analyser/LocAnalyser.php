<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Analyser;

use Bartlett\Reflect\Application\Tokenizer\DefaultTokenizer;

use PhpParser\Node;

/**
 * This analyzer collects different size metrics about lines of code.
 *
 * It analyse source code like Sebastian Bergmann phploc solution
 * (https://github.com/sebastianbergmann/phploc), and give a text report
 * as follow :
 *
 * <code>
 * Directories                                         50
 * Files                                              374
 *
 * Size
 *   Lines of Code (LOC)                             1858
 *   Comment Lines of Code (CLOC)                     560 (30.14%)
 *   Non-Comment Lines of Code (NCLOC)               1298 (69.86%)
 *   Logical Lines of Code (LLOC)                     289 (15.55%)
 *     Classes                                        260 (89.97%)
 *       Average Class Length                          37
 *       Average Method Length                          9
 *     Functions                                        5 (1.73%)
 *       Average Function Length                        5
 *     Not in classes or functions                     24 (8.30%)
 * </code>
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

class LocAnalyser extends AbstractAnalyser
{
    private $llocClasses;
    private $llocFunctions;

    public function __construct()
    {
        $this->metrics = [
            'llocClasses'   => 0,
            'llocByNoc'     => 0,
            'llocByNom'     => 0,
            'llocFunctions' => 0,
            'llocByNof'     => 0,
            'llocGlobal'    => 0,
            'classes'       => 0,
            'functions'     => 0,
            'methods'       => 0,
            'cloc'          => 0,
            'eloc'          => 0,
            'lloc'          => 0,
            'wloc'          => 0,
            'loc'           => 0,
            'ccn'           => 0,
            'ccnMethods'    => 0,
        ];
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        if ($node instanceof Node\Stmt\Class_) {
            parent::visitClass($node);
            $this->llocClasses = 0;

        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            if (!$this->testClass) {
                $this->visitMethod($node);
            }

        } elseif ($node instanceof Node\Stmt\Function_
            || $node instanceof Node\Expr\Closure
        ) {
            $this->llocFunctions = 0;
        }
    }

    public function leaveNode(Node $node)
    {
        parent::leaveNode($node);

        if ($node instanceof Node\Stmt\Class_) {
            if (!$this->testClass) {
                $this->visitClass($node);
            }

        } elseif ($node instanceof Node\Stmt\Function_
            || $node instanceof Node\Expr\Closure
        ) {
            $this->visitFunction($node);
        }
    }

    /**
     * Explore each user classes found in the current namespace.
     *
     * @param Node\Stmt\Class_ $class The current user class explored
     *
     * @return void
     */
    protected function visitClass(Node\Stmt\Class_ $class): void
    {
        $this->metrics['classes']++;
        $this->metrics['llocClasses'] += $this->llocClasses;
    }

    /**
     * Explore methods of each user classes
     * found in the current namespace.
     *
     * @param Node\Stmt\ClassMethod $method The current method explored
     *
     * @return void
     */
    protected function visitMethod(Node\Stmt\ClassMethod $method): void
    {
        $this->metrics['methods']++;

        if (count($method->stmts) === 0) {
            // abstract or interface methods (without implementation)
            return;
        }

        $lines = $this->getLinesOfCode($method->stmts);

        foreach ($lines as $key => $count) {
            $this->metrics[$key] += $count;
        }

        $this->llocClasses += $lines['lloc'];

        $this->metrics['ccnMethods'] += $lines['ccn'];
    }

    /**
     * Explore user functions found in the current namespace.
     *
     * @param Node $function The current user function explored
     *
     * @return void
     */
    protected function visitFunction(Node $function): void
    {
        $this->metrics['functions']++;

        if (count($function->stmts) === 0) {
            // without implementation
            return;
        }

        $lines = $this->getLinesOfCode($function->stmts);

        foreach ($lines as $key => $count) {
            $this->metrics[$key] += $count;
        }

        $this->metrics['llocFunctions'] += $lines['lloc'];
    }

    /**
     * Counts the Comment Lines Of Code (CLOC) and a pseudo Executable Lines Of
     * Code (ELOC) values.
     *
     * ELOC = Non Whitespace Lines + Non Comment Lines
     *
     * <code>
     * array(
     *     cloc  =>  23,  // Comment Lines Of Code
     *     eloc  =>  42   // Executable Lines Of Code
     *     lloc  =>  57   // Logical Lines Of Code
     * )
     * </code>
     *
     * This code has been copied and adapted from pdepende/pdepend
     *
     * @param array $nodes AST of the current chunk of code
     *
     * @return array
     */
    private function getLinesOfCode(array $nodes): array
    {
        $length = $nodes[count($nodes)-1]->getAttribute('endTokenPos')
            - $nodes[0]->getAttribute('startTokenPos')
            + 1
        ;
        $tokens = array_slice(
            $this->tokens,
            $nodes[0]->getAttribute('startTokenPos'),
            $length
        );

        $tokenizer = new DefaultTokenizer();
        // normalize the raw token stream
        $tokenizer->setTokens($tokens);

        $tokens = $tokenizer->getTokens();

        $loc = $nodes[count($nodes)-1]->getAttribute('endLine')
            - $nodes[0]->getAttribute('startLine')
            + 1
        ;
        $clines = 0;
        $llines = 0;
        $wlines = 0;
        $ccn    = 0;

        for ($i = 0, $max = count($tokens); $i < $max; $i++) {
            if ($tokens[$i][0] === 'T_COMMENT') {
                ++$clines;

            } elseif ($tokens[$i][0] === 'T_DOC_COMMENT') {
                $clines += substr_count($tokens[$i][1], "\n") + 1;

            } elseif ($tokens[$i][0] == 'T_WHITESPACE') {
                $lines = substr_count($tokens[$i][1], "\n");
                if ($lines > 0) {
                    $wlines += --$lines;
                }
            }

            switch ($tokens[$i][0]) {
                case 'T_QUESTION_MARK':
                case 'T_IF':
                case 'T_ELSEIF':
                case 'T_FOR':
                case 'T_FOREACH':
                case 'T_WHILE':
                case 'T_SWITCH':
                case 'T_CASE':
                case 'T_DEFAULT':
                case 'T_TRY':
                case 'T_CATCH':
                case 'T_BOOLEAN_AND':
                case 'T_LOGICAL_AND':
                case 'T_BOOLEAN_OR':
                case 'T_LOGICAL_OR':
                case 'T_GOTO':
                case 'T_FUNCTION':
                    ++$ccn;
                    break;
                case 'T_SEMICOLON':
                    ++$llines;
                    break;
            }
        }

        $lines = array(
            'cloc' => $clines,
            'eloc' => $loc - $wlines - $clines,
            'lloc' => $llines,
            'wloc' => $wlines,
            'loc'  => $loc,
            'ccn'  => $ccn,
        );
        return $lines;
    }
}
