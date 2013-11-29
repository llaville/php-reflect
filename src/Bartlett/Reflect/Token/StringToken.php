<?php

namespace Bartlett\Reflect\Token;

class StringToken extends TokenWithArgument
{
    protected $arguments;
    protected $name;

    public function getName()
    {
        if ($this->name !== NULL) {
            return $this->name;
        }

        if (($this->tokenStream[$this->id+1][0] == 'T_OPEN_BRACKET'
            || $this->tokenStream[$this->id+2][0] == 'T_OPEN_BRACKET')
            && $this->tokenStream[$this->id-2][0] !== 'T_NEW'
            && $this->tokenStream[$this->id-2][0] !== 'T_FUNCTION'
        ) {
            $this->name = $this->text;
        }

        return $this->name;
    }

}