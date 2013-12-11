<?php

namespace Bartlett\Reflect\Token;

class InterfaceToken extends TokenWithScope
{
    protected $interfaces;

    public function getName()
    {
        $token = $this->tokenStream[$this->id + 2];
        $text  = $token[1];
        return $text;
    }

    public function hasParent()
    {
        return
            (isset($this->tokenStream[$this->id + 4]) &&
            $this->tokenStream[$this->id + 4][0] == 'T_EXTENDS');
    }

    public function getParent()
    {
        if (!$this->hasParent()) {
            return false;
        }

        $i         = $this->id + 6;
        $className = $this->tokenStream[$i][1];

        while (isset($this->tokenStream[$i+1]) &&
            $this->tokenStream[$i+1][0] != 'T_WHITESPACE'
        ) {
            $className .= $this->tokenStream[++$i][1];
        }

        return $className;
    }

    public function getPackage()
    {
        $className  = $this->getName();
        $docComment = $this->getDocblock();

        $result = array(
            'namespace'   => '',
            'fullPackage' => '',
            'category'    => '',
            'package'     => '',
            'subpackage'  => ''
        );

        for ($i = $this->id; $i; --$i) {
            if ($this->tokenStream[$i][0] == 'T_NAMESPACE') {
                $ns = new NamespaceToken(
                    $this->tokenStream[$i][1],
                    $this->tokenStream[$i][2],
                    $i,
                    $this->tokenStream
                );
                $result['namespace'] = $ns->getName();
                break;
            }
        }

        if (preg_match('/@category[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['category'] = $matches[1];
        }

        if (preg_match('/@package[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['package']     = $matches[1];
            $result['fullPackage'] = $matches[1];
        }

        if (preg_match('/@subpackage[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['subpackage']   = $matches[1];
            $result['fullPackage'] .= '.' . $matches[1];
        }

        if (empty($result['fullPackage'])) {
            $result['fullPackage'] = $this->arrayToName(
                explode('_', str_replace('\\', '_', $className)),
                '.'
            );
        }

        return $result;
    }

    protected function arrayToName(array $parts, $join = '\\')
    {
        $result = '';

        if (count($parts) > 1) {
            array_pop($parts);

            $result = join($join, $parts);
        }

        return $result;
    }

    public function hasInterfaces()
    {
        if ((isset($this->tokenStream[$this->id + 4])
            && $this->tokenStream[$this->id + 4][0] == 'T_IMPLEMENTS') ||
           (isset($this->tokenStream[$this->id + 8])
            && $this->tokenStream[$this->id + 8][0] == 'T_IMPLEMENTS')
        ) {
            return true;
        }
        return false;
    }

    public function getInterfaces()
    {
        if ($this->interfaces !== null) {
            return $this->interfaces;
        }

        if (!$this->hasInterfaces()) {
            return ($this->interfaces = array());
        }

        if ($this->tokenStream[$this->id + 4][0] == 'T_IMPLEMENTS') {
            $i = $this->id + 3;
        } else {
            $i = $this->id + 7;
        }

        while ($this->tokenStream[$i+1][0] != 'T_OPEN_CURLY') {
            $i++;
            if ($this->tokenStream[$i][0] == 'T_STRING') {
                $this->interfaces[] = $this->tokenStream[$i][1];
            }
        }
        return $this->interfaces;
    }
}
