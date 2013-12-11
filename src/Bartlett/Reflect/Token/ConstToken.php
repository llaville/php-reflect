<?php

namespace Bartlett\Reflect\Token;

class ConstToken extends TokenWithScope
{
    protected $name;
    protected $value;

    public function getName()
    {
        if ($this->name !== null) {
            return $this->name;
        }
        $this->name = $this->tokenStream[$this->id + 2][1];

        for ($i = $this->id + 3; $i < $this->id + 7; $i++) {
            if (!isset($this->tokenStream[$i])) {
                return;
            }

            if ($this->tokenStream[$i][0] == 'T_EQUAL'
                || $this->tokenStream[$i][0] == 'T_WHITESPACE'
            ) {
                continue;
            }

            if ($this->tokenStream[$i][0] == 'T_CONSTANT_ENCAPSED_STRING') {
                $this->value = trim($this->tokenStream[$i][1], "'\"");
            } elseif ($this->tokenStream[$i][0] !== 'T_SEMICOLON') {
                $this->value = $this->tokenStream[$i][1];
            }
            break;
        }
        return $this->name;
    }

    public function getValue()
    {
        $this->getName();
        return $this->value;
    }
}
