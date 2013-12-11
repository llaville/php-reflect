<?php

namespace Bartlett\Reflect\Token;

class NamespaceToken extends TokenWithScope
{
    protected $namespace;
    protected $alias;

    public function getName()
    {
        if ($this->namespace !== null) {
            return $this->namespace;
        }

        $this->namespace = $this->tokenStream[$this->id+2][1];

        for ($i = $this->id + 3;; $i += 2) {
            if (!isset($this->tokenStream[$i])) {
                break;
            }
            if ($this->tokenStream[$i][0] == 'T_NS_SEPARATOR') {
                $this->namespace .= '\\' . $this->tokenStream[$i+1][1];
            } else {
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

        $this->getName();

        $tmp         = explode('\\', $this->namespace);
        $this->alias = array_pop($tmp);

        return $this->alias;
    }

    public function isImported()
    {
        return false;
    }
}
