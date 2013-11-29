<?php

namespace Bartlett\Reflect\Model;

class FunctionModel
    extends AbstractFunctionModel
    implements Visitable
{

    /**
     * Constructs a new FunctionModel instance.
     */
    public function __construct($qualifiedName)
    {
        parent::__construct();

        $this->name = $qualifiedName;

        $this->struct['arguments'] = array();

        $parts = explode('\\', $qualifiedName);
        $this->short_name = array_pop($parts);
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
            foreach($parameters as $parameter) {
                $str .= '    ' . $parameter->__toString();
            }
            $str .= '  }' . $eol;
        }
        $str .= '}' . $eol;

        return $str;
    }

}
