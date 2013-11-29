<?php

namespace Bartlett\Reflect\Token;

abstract class TokenWithInclude extends TokenWithScope
{
    protected $name;
    protected $type;

    public function getName()
    {
        if ($this->name !== NULL) {
            return $this->name;
        }

        $i = $this->id + 1;
        while (isset($this->tokenStream[$i])
            && $this->tokenStream[$i][0] !== 'T_SEMICOLON') {
            if ($this->tokenStream[$i][0] == 'T_CONSTANT_ENCAPSED_STRING') {
                $this->name .= trim($this->tokenStream[$i][1], "'\"");
            } elseif ($this->tokenStream[$i][0] == 'T_VARIABLE') {
                $this->name .= $this->tokenStream[$i][1] . ' ';
            }
            $i++;
        }
        if ($this->name !== NULL) {
            $this->type = strtolower(
                str_replace('T_', '', $this->tokenStream[$this->id][0])
            );
        }
        return trim($this->name);
    }

    public function getType()
    {
        $this->getName();
        return $this->type;
    }
}
