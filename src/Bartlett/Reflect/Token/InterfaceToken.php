<?php
/**
 * InterfaceToken represents the T_INTERFACE token.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @link     http://www.php.net/manual/en/tokens.php
 */

namespace Bartlett\Reflect\Token;

/**
 * Reports information about an interface.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class InterfaceToken extends TokenWithScope
{
    protected $interfaces;

    /**
     * Gets the name of interface.
     *
     * @return string
     */
    public function getName()
    {
        $token = $this->tokenStream[$this->id + 2];
        $text  = $token[1];
        return $text;
    }

    /**
     * Checks if interface extends another.
     *
     * @return bool TRUE if a parent exists, FALSE otherwise
     */
    public function hasParent()
    {
        return
            (isset($this->tokenStream[$this->id + 4]) &&
            $this->tokenStream[$this->id + 4][0] == 'T_EXTENDS');
    }

    /**
     * Gets the name of the parent interface.
     *
     * @return string
     */
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

    /**
     * Returns DocBlock (if any) that identify this interface
     *
     * @return array
     */
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

    /**
     * Checks if this interface implement others interfaces.
     *
     * @return bool TRUE if implements others interfaces, FALSE otherwise
     */
    public function hasInterfaces()
    {
        if ((isset($this->tokenStream[$this->id + 4])
            && $this->tokenStream[$this->id + 4][0] == 'T_IMPLEMENTS')
            || (isset($this->tokenStream[$this->id + 8])
            && $this->tokenStream[$this->id + 8][0] == 'T_IMPLEMENTS')
        ) {
            return true;
        }
        return false;
    }

    /**
     * Returns list of others interfaces implemented.
     *
     * @return array
     */
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
