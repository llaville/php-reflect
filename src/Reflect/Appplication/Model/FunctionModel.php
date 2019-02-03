<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Model;

/**
 * The FunctionModel class reports information about a function.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class FunctionModel extends AbstractFunctionModel
{
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

        // parameters
        $parameters = $this->getParameters();
        if (count($parameters)) {
            $str .= sprintf(
                '%s  - Parameters [%d] {%s',
                $eol,
                count($parameters),
                $eol
            );
            foreach ($parameters as $parameter) {
                $str .= '    ' . (string) $parameter;
            }
            $str .= '  }' . $eol;
        }

        $str .= '}' . $eol;

        return $str;
    }
}
