<?php
/**
 * FunctionModel represents a function definition.
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

namespace Bartlett\Reflect\Model;

/**
 * The FunctionModel class reports information about a function.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class FunctionModel extends AbstractFunctionModel implements Visitable
{
    /**
     * Constructs a new FunctionModel instance.
     *
     * @param string $qualifiedName The full qualified name of the function
     */
    public function __construct($attributes)
    {
        $qualifiedName = $attributes['name'];
        //unset($attributes['name']);

        $struct = array(
            'docComment' => '',
            'startLine'  => 0,
            'endLine'    => 0,
            'file'       => '',
            'extension'  => 'user',
            'closure'    => false,
            'arguments'  => array(),
        );

        parent::__construct(
            'UserFunction',
            array_merge($struct, $attributes)
        );

        $this->name = $qualifiedName;

        $parts = explode('\\', $qualifiedName);
        $this->short_name = array_pop($parts);

        $this->struct['namespace'] = implode('\\', $parts);
    }

    /**
     * Returns the string representation of the FunctionModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";
        $str = '';
        $str .= sprintf(
            'Function [ <%s> function %s ] {%s',
            $this->getExtensionName(),
            $this->getName(),
            $eol
        );

        $str .= sprintf(
            '  @@ %s %d - %d%s',
            $this->getFileName(),
            $this->getStartLine(),
            $this->getEndLine(),
            $eol
        );

        $parameters = $this->getParameters();
        if (count($parameters)) {
            $str .= sprintf(
                '%s  - Parameters [%d] {%s',
                $eol,
                count($parameters),
                $eol
            );
            foreach ($parameters as $parameter) {
                $str .= '    ' . $parameter->__toString();
            }
            $str .= '  }' . $eol;
        }
        $str .= '}' . $eol;

        return $str;
    }
}
