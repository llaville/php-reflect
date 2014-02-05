<?php
/**
 * Expression represents a node expression of the AST.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Ast;

/**
 * The Expression class represents a node expression of the AST (Abstract Syntax Tree).
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
class Expression extends AbstractNode
{
    /**
     * Ast Expression class constructor
     *
     * @param string $type       Identify the Statement node family
     * @param array  $attributes List of attributes for this node
     */
    public function __construct($type, $attributes = array())
    {
        parent::__construct(
            $type,
            array_merge(array('startLine' => 0, 'endLine' => 0), $attributes)
        );
    }
}
