<?php

namespace Bartlett\Reflect\Token;

class FunctionToken extends TokenWithArgument
{
    protected $ccn;
    protected $name;
    protected $signature;
    protected $closure;

    public function getName()
    {
        if ($this->name !== null) {
            return $this->name;
        }
        $this->closure = false;

        for ($i = $this->id + 1; $i < count($this->tokenStream); $i++) {
            if ($this->tokenStream[$i][0] == 'T_STRING') {
                $this->name = $this->tokenStream[$i][1];
                break;

            } elseif ($this->tokenStream[$i][0] == 'T_AMPERSAND'
                && $this->tokenStream[$i][0] == 'T_STRING'
            ) {
                $this->name = $this->tokenStream[$i+1][1];
                break;

            } elseif ($this->tokenStream[$i][0] == 'T_OPEN_BRACKET') {
                $this->name = '';
                $this->closure = true;
                break;
            }
        }

        return $this->name;
    }

    public function getCCN()
    {
        if ($this->ccn !== null) {
            return $this->ccn;
        }

        $this->ccn = 1;
        $end       = $this->getEndTokenId();

        for ($i = $this->id; $i <= $end; $i++) {
            switch ($this->tokenStream[$i][0]) {
                case 'T_IF':
                case 'T_ELSEIF':
                case 'T_FOR':
                case 'T_FOREACH':
                case 'T_WHILE':
                case 'T_CASE':
                case 'T_CATCH':
                case 'T_BOOLEAN_AND':
                case 'T_LOGICAL_AND':
                case 'T_BOOLEAN_OR':
                case 'T_LOGICAL_OR':
                case 'T_QUESTION_MARK':
                    $this->ccn++;
                    break;
            }
        }

        return $this->ccn;
    }

    public function getSignature()
    {
        if ($this->signature !== null) {
            return $this->signature;
        }

        $this->signature = '';

        $i = $this->id + 2;

        while ($this->tokenStream[$i][0] != 'T_OPEN_CURLY'
            && $this->tokenStream[$i][0] != 'T_SEMICOLON'
        ) {
            $this->signature .= $this->tokenStream[$i++][1];
        }

        return trim($this->signature);
    }

    public function isClosure()
    {
        return $this->closure;
    }
}
