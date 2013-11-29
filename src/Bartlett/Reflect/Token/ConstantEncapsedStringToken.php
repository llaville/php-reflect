<?php
namespace Bartlett\Reflect\Token;

class ConstantEncapsedStringToken extends TokenWithScope
{
    protected $name;
    protected $type;

    public function getName()
    {
        static $const = array('define', 'defined', 'constant');

        if ($this->name !== NULL) {
            return $this->name;
        }

        for ($i = $this->id - 1; $i > $this->id - 5; $i -= 1) {
            if (!isset($this->tokenStream[$i])) {
                return;
            }
            if ($this->tokenStream[$i][0] !== 'T_OPEN_BRACKET'
                && $this->tokenStream[$i][0] !== 'T_WHITESPACE'
                && $this->tokenStream[$i][0] !== 'T_STRING'
            ) {
                /*
                    look for signatures
                        constant ( "CONSTANT" )
                        define ( 'CONSTANT' )
                        defined('CONSTANT')
                 */
                return;
            }
            if (in_array(strtolower($this->tokenStream[$i][1]), $const)) {
                $this->type = 'constant';
                $this->name = trim($this->tokenStream[$this->id][1], "'\"");
                break;
            }
        }
        return $this->name;
    }

    public function getType()
    {
        $this->getName();
        return $this->type;
    }

    public function getValue()
    {
        for ($i = $this->id + 1; $i < $this->id + 5; $i++) {
            if ($this->tokenStream[$i][0] == 'T_COMMA') {
                $j = $i + 1;
                if ($this->tokenStream[$j][0] == 'T_WHITESPACE') {
                    $j++;
                }
                return trim($this->tokenStream[$j][1], "'\"");
            }
        }
    }

}
