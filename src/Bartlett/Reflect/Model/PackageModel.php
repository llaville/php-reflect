<?php
/**
 * PackageModel represents a package/namespace definition.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Model;

use Bartlett\Reflect\Ast\AbstractNode;
use Bartlett\Reflect\Ast\Statement;
use Bartlett\Reflect\Ast\Expression;
use Bartlett\Reflect\Filter\ClassFilter;
use Bartlett\Reflect\Filter\InterfaceFilter;
use Bartlett\Reflect\Filter\TraitFilter;
use Bartlett\Reflect\Filter\FunctionFilter;
use Bartlett\Reflect\Filter\ConstantFilter;
use Bartlett\Reflect\Filter\IncludeFilter;

/**
 * The PackageModel class reports information about a package/namespace.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class PackageModel extends AbstractNode implements Visitable
{
    /**
     * Constructs a new PackageModel instance.
     *
     * @param string $name Name of the package or namespace
     */
    public function __construct($attributes)
    {
        $name = $attributes['name'];
        unset($attributes['name']);

        $struct = array(
            'docComment'   => '',
            'startLine'    => 0,
            'endLine'      => 0,
            'file'         => '',
            'import'       => false,
            'alias'        => '',
            'dependencies' => false,
        );

        parent::__construct(
            'Namespace',
            array_merge($struct, $attributes)
        );

        $this->name = $name;
    }

    /**
     * Gets the classes defined on this namespace.
     *
     * @return ClassFiler iterator that list ClassModel objects reflecting each class.
     */
    public function getClasses(array $modifiers = null)
    {
        $iterator = new ClassFilter($this->getChildren(), $modifiers);
        return $iterator;
    }

    /**
     * Gets the interfaces defined on this namespace.
     *
     * @return InterfaceFilter iterator that list ClassModel objects reflecting each interface.
     */
    public function getInterfaces()
    {
        $iterator = new InterfaceFilter($this->getChildren());
        return $iterator;
    }

    /**
     * Gets the traits defined on this namespace.
     *
     * @return TraitFilter iterator that list ClassModel objects reflecting each trait.
     */
    public function getTraits()
    {
        $iterator = new TraitFilter($this->getChildren());
        return $iterator;
    }

    /**
     * Gets the user-functions defined on this namespace.
     *
     * @return FunctionFilter iterator that list FunctionModel objects reflecting each function.
     */
    public function getFunctions()
    {
        $iterator = new FunctionFilter($this->getChildren());
        return $iterator;
    }

    /**
     * Gets the (user|magic) constants defined on this namespace.
     *
     * @return ConstantFilter iterator that list ConstantModel objects reflecting each constant.
     */
    public function getConstants()
    {
        $nodes = array();

        $this->findChildren(
            'Bartlett\\Reflect\\Model\\ConstantModel',
            'Constant',
            $nodes
        );

        $iterator = new ConstantFilter(new \ArrayIterator($nodes));
        return $iterator;
    }

    /**
     * Gets the includes used on this namespace.
     *
     * @return IncludeFilter iterator that list IncludeModel objects reflecting each constant.
     */
    public function getIncludes()
    {
        $iterator = new IncludeFilter($this->getChildren());
        return $iterator;
    }

    /**
     * Gets internal components provided by PHP extensions.
     */
    public function getDependencies()
    {
        if ($this->struct['dependencies'] === false) {
            // mapping of dependencies for lazy loading
            $this->struct['dependencies'] = array();

            $this->findChildren(
                'Bartlett\\Reflect\\Ast\\Statement',
                'Internal',
                $this->struct['dependencies']
            );

            $this->findChildren(
                'Bartlett\\Reflect\\Model\\ConstantModel',
                'Constant',
                $this->struct['dependencies']
            );

            $this->findChildren(
                'Bartlett\\Reflect\\Ast\\Expression',
                'Alloc',
                $this->struct['dependencies']
            );

            $this->findChildren(
                'Bartlett\\Reflect\\Ast\\Expression',
                'MethodCall',
                $this->struct['dependencies']
            );

            $this->findChildren(
                'Bartlett\\Reflect\\Ast\\Expression',
                'ClassMemberAccessOnInstantiation',
                $this->struct['dependencies']
            );
        }
        return $this->struct['dependencies'];
    }

    /**
     * Get a Doc comment from a namespace.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->struct['docComment'];
    }

    /**
     * Gets the starting line number of the namespace.
     *
     * @return int
     * @see    PackageModel::getEndLine()
     */
    public function getStartLine()
    {
        return $this->struct['startLine'];
    }

    /**
     * Gets the ending line number of the namespace.
     *
     * @return int
     * @see    PackageModel::getStartLine()
     */
    public function getEndLine()
    {
        return $this->struct['endLine'];
    }

    /**
     * Gets the file name from a user-defined namespace.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->struct['file'];
    }

    /**
     * Gets the full name of the namespace.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the short name (alias) of the namespace.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->struct['alias'];
    }

    /**
     * Checks if the package is imported or declared
     *
     * @return bool
     */
    public function isImported()
    {
        return $this->struct['import'];
    }

    /**
     * Returns the string representation of the PackageModel object.
     *
     * @return string
     */
    public function __toString()
    {
        $eol = "\n";
        $str = '';
        $str .= sprintf(
            'Package [ %s ] {%s',
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

        $str .= '}' . $eol;

        return $str;
    }
}
