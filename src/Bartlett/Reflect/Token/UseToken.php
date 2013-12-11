<?php

namespace Bartlett\Reflect\Token;

class UseToken extends TokenWithScope
{
    protected $trait;
    protected $namespace;
    protected $alias;

    public function getName($class)
    {
        if ($class === false) {
            return $this->getNamespace();
        }

        return $this->getTrait();
    }

    protected function getTrait()
    {
        if ($this->trait !== null) {
            return $this->trait;
        }

        $this->trait = array();

        for ($i = $this->id + 2;; $i++) {
            if ($this->tokenStream[$i][0] == 'T_STRING') {
                $this->trait[] = $this->tokenStream[$i][1];

            } elseif ($this->tokenStream[$i][0] == 'T_SEMICOLON'
                || $this->tokenStream[$i][0] == 'T_OPEN_CURLY'
            ) {
                break;
            }
        }
        return $this->trait;
    }

    protected function getNamespace()
    {
        if ($this->namespace !== null) {
            return $this->namespace;
        }

        $i = $this->id + 2;

        if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
            $this->namespace = '';
        } else {
            $this->namespace = $this->tokenStream[$i][1];
            $i++;
        }

        for (;; $i += 2) {
            if (!isset($this->tokenStream[$i])) {
                break;
            }
            if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
                $this->namespace .= '\\' . $this->tokenStream[$i+1][1];
            } elseif ($this->tokenStream[$i][0] !== 'T_STRING') {

                if ($this->tokenStream[$i+1][0] == 'T_AS') {
                    $this->alias = $this->tokenStream[$i+3][1];
                }
                break;
            }
        }

        return $this->namespace;
    }

    public function getAlias()
    {
        if ($this->alias !== null) {
            return $this->alias;
        }

        $this->getName(true);

        $tmp         = explode('\\', $this->namespace);
        $this->alias = array_pop($tmp);

        return $this->alias;

    }

    public function isImported()
    {
        return true;
    }
}
