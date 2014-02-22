<?php
/**
 * Plugin to make PlantUML diagrams (http://plantuml.sourceforge.net/).
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

namespace Bartlett\Reflect\Plugin\PlantUML;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

use Bartlett\Reflect\Visitor\AbstractVisitor;

/**
 * Plugin to make PlantUML diagrams that reflect data source code analysed.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC3
 */
class PlantUMLPlugin extends AbstractVisitor implements EventSubscriberInterface
{
    protected $packages;
    protected $pkgid;
    protected $clsid;

    /**
     * Initializes the PlantUML plugin.
     */
    public function __construct()
    {
        $this->packages = array();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'reflect.complete' => 'onReflectComplete',
        );
    }

    /**
     * Analyse metrics at end of parsing a full data source.
     *
     * @param Event $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectComplete(Event $event)
    {
        $reflect = $event->getSubject();

        foreach ($reflect->getPackages() as $package) {
            $package->accept($this);
        }
    }

    /**
     * Explore each namespace (PackageModel) of the data source.
     *
     * @param object $package Reflect the current namespace explored
     *
     * @return void
     */
    public function visitPackageModel($package)
    {
        $this->pkgid = md5($package->getName());

        $this->packages[$this->pkgid] = array(
            'name'    => str_replace('\\', '\\\\', $package->getName()),
            'classes' => array(),
        );

        foreach ($package->getClasses() as $class) {
            $class->accept($this);
        }
    }

    /**
     * Explore all classes (ClassModel) in each namespace (PackageModel).
     *
     * @param object $class Reflect the current class explored
     *
     * @return void
     */
    public function visitClassModel($class)
    {
        if ($class->isAbstract()) {
            $interface = 'abstract';
        } elseif ($class->isInterface()) {
            $interface = 'interface';
        } else {
            $interface = 'class';
        }

        if ($parent = $class->getParentClass()) {
            $parent = $parent->getShortName();
        }

        $this->clsid = md5($class->getName());

        $this->packages[$this->pkgid]['classes'][$this->clsid] = array(
            'type'       => $interface,
            'name'       => $class->getShortName(),
            'parent'     => $parent,
            'interfaces' => $class->getInterfaceNames(),
            'properties' => array(),
            'methods'    => array(),
        );

        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
    }

    /**
     * Explore all properties (PropertyModel) in each class (ClassModel).
     *
     * @param object $property Reflect the current property explored
     *
     * @return void
     */
    public function visitPropertyModel($property)
    {
        if ($property->isPrivate()) {
            $visibility = '-';
        } elseif ($property->isProtected()) {
            $visibility = '#';
        } else {
            $visibility = '+';
        }

        $this->packages[$this->pkgid]['classes'][$this->clsid]['properties'][] = array(
            'visibility' => $visibility,
            'name'       => $property->getName(),
        );
    }

    /**
     * Explore all methods (MethodModel) of each class, interface or trait.
     *
     * @param object $class Reflect the current class explored
     *
     * @return void
     */
    public function visitMethodModel($method)
    {
        if ($method->isPrivate()) {
            $visibility = '-';
        } elseif ($method->isProtected()) {
            $visibility = '#';
        } else {
            $visibility = '+';
        }

        $this->packages[$this->pkgid]['classes'][$this->clsid]['methods'][] = array(
            'visibility' => $visibility,
            'name'       => $method->getShortName(),
        );
    }

    public function visitConstantModel($constant)
    {
    }

    public function visitFunctionModel($function)
    {
    }

    public function visitIncludeModel($include)
    {
    }

    public function visitDependencyModel($dependency)
    {
    }

    /**
     * Build an UML package diagram.
     *
     * @param string $packageName The Package/Namespace name
     *
     * @throws \DomainException if package does not exists
     *
     * @link http://plantuml.sourceforge.net/classes.html#Using
     */
    public function getPackageDiagram($packageName)
    {
        if (!isset($this->packages[ md5($packageName) ])) {
            throw new \DomainException(
                'Package "' . $packageName . '" does not exists.'
            );
        }

        $eol  = "\n";
        $diag = $eol;

        $packageValues = $this->packages[ md5($packageName) ];

        $diag .= sprintf(
            'package "%s" {%s',
            $packageValues['name'],
            $eol
        );

        foreach ($packageValues['classes'] as $classValues) {
            $diag .= sprintf(
                '%s %s%s',
                $classValues['type'],
                $classValues['name'],
                $eol
            );
        }

        $diag .= sprintf('}%s', $eol);

        return $diag;
    }

    /**
     * Build an UML class diagram.
     *
     * @param string $qualifiedClass The fully qualified class name
     *
     * @link http://plantuml.sourceforge.net/classes.html
     */
    public function getClassDiagram($qualifiedClass)
    {
        $parts       = explode('\\', $qualifiedClass);
        $className   = array_pop($parts);
        $packageName = implode('\\', $parts);

        if (empty($packageName)) {
            $packageName = '+global';
        }

        $packageValues = $this->packages[ md5($packageName) ];
        $classValues   = $packageValues['classes'][ md5($qualifiedClass) ];

        $eol  = "\n";
        $diag = $eol;

        $diag .= sprintf(
            '%s %s{%s',
            $classValues['type'],
            $classValues['name'],
            $eol
        );

        // prints class properties
        foreach ($classValues['properties'] as $property) {
            $diag .= sprintf(
                '    %s%s%s',
                $property['visibility'],
                $property['name'],
                $eol
            );
        }

        // prints class methods
        foreach ($classValues['methods'] as $method) {
            $diag .= sprintf(
                '    %s%s()%s',
                $method['visibility'],
                $method['name'],
                $eol
            );
        }

        $diag .= sprintf('}%s', $eol);

        // prints inheritance (if exists)
        if ($classValues['parent']) {
            $diag .= sprintf(
                '%s <|-- %s%s',
                $classValues['parent'],
                $classValues['name'],
                $eol
            );
        }

        // prints interfaces (if exists)
        foreach ($classValues['interfaces'] as $interface) {
            // print signature just to be identified as interface
            $diag .= sprintf(
                'interface %s {%s',
                $interface,
                $eol
            );
            $diag .= sprintf('}%s', $eol);

            $diag .= sprintf(
                '%s <|.. %s%s',
                $interface,
                $classValues['name'],
                $eol
            );
        }

        return $diag;
    }
}
