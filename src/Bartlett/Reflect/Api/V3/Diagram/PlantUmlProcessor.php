<?php
/**
 * PlantUml diagram processor
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 * @link     http://plantuml.sourceforge.net/
 */

namespace Bartlett\Reflect\Api\V3\Diagram;

use Bartlett\Reflect\Model\ClassModel;

/**
 * Diagram processor interface
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-beta3
 */
class PlantUmlProcessor implements ProcessorInterface
{
    const GLOBAL_NAMESPACE = 'global';

    private $undeclaredObjects;
    private $undeclared;
    private $models;

    /**
     * {@inheritdoc}
     */
    public function render($models, $class)
    {
        $this->undeclaredObjects = array();
        $this->undeclared        = '';
        $this->models            = $models;

        $eol  = "\n";
        $diag = $eol;

        if (null !== $class) {
            // print class diagram of a unique class

            $package = str_replace('\\', '.', $class->getNamespaceName());

            $diag .= sprintf(
                'namespace %s #DDDDDD {%s',
                $package,
                $eol
            );

            $diag .= $this->getClassDiagram($class);

            // end of previous namespace
            $diag .= sprintf('}%s', $eol);

        } else {
            // print namespace(s) diagram

            $namespaces = array();

            foreach ($models as $model) {
                $package = $model->getNamespaceName();
                // proceed objects of a same namespace
                if (in_array($package, $namespaces)) {
                    continue;
                }
                $namespaces[] = $package;

                $collect = $this->getNamespaceModel($package);

                $package = str_replace('\\', '.', $package);

                $diag .= sprintf(
                    'namespace %s {%s',
                    $package,
                    $eol
                );

                // print each class/interface in current namespace
                foreach ($collect as $class) {
                    $diag .= $this->getClassDiagram($class);

                }

                // end of previous namespace
                $diag .= sprintf('}%s', $eol);
            }
        }

        // print all undeclared objects (class, interface)
        $diag .= $this->undeclared;

        return $diag;
    }

    /**
     * Return a collection of Bartlett\Reflect\Model objects
     * corresponding to a single namespace
     *
     * @param string $namespace Common namespace of objects to filter
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    protected function getNamespaceModel($namespace)
    {
        $collect = $this->models->filter(
            function($element) use ($namespace) {
                return $element->getNamespaceName() === $namespace;
            }
        );

        if (count($collect) === 0) {
            throw new \Exception(
                sprintf('Namespace "%s" not found.', $namespace)
            );
        }
        return $collect;
    }

    /**
     * Return a collection of Bartlett\Reflect\Model objects
     * corresponding to a single namespace
     *
     * @param string $argument Class to retrieve
     * @param array  $models   Collection of the API response
     *
     * @return Bartlett\Reflect\Model\ClassModel
     */
    protected function getClassModel($argument)
    {
        $collect = $this->models->filter(
            function($element) use ($argument) {
                return $element instanceof ClassModel
                    && $element->getName() === $argument;
            }
        );

        if (count($collect) === 0) {
            throw new \Exception(
                sprintf('Class "%s" not found.', $argument)
            );
        }
        return $collect->first();
    }

    /**
     * Return plantuml string that define the $model class
     *
     * @param Bartlett\Reflect\Model\ClassModel $model  Class|Interface to introspect
     *
     * @return string
     */
    protected function getClassDiagram($model)
    {
        $eol  = "\n";
        $diag = '';

        // prints inheritance (if exists)
        $argument = $model->getParentClass();

        if ($argument) {
            try {
                $parent      = $this->getClassModel($argument);
                $longName    = $parent->getName();
                $undeclared  = false;

            } catch (\Exception $e) {
                // class is undeclared in data source
                $parts       = explode('\\', $argument);
                $shortName   = array_pop($parts);
                $longName    = $argument;
                $ns          = implode('.', $parts);
                $undeclared  = true;
            }

            if ($undeclared && !in_array($argument, $this->undeclaredObjects)) {
                if (empty($ns)) {
                    $ns = self::GLOBAL_NAMESPACE;
                }
                $this->undeclaredObjects[] = $argument;
                $this->undeclared .= sprintf(
                    '%snamespace %s %s {%s',
                    $eol,
                    $ns,
                    $undeclared ? '#EB937F' : '',
                    $eol
                );
                $this->undeclared .= sprintf(
                    'class %s%s',
                    $shortName,
                    $eol
                );
                $this->undeclared .= sprintf('}%s', $eol);
            }

            $diag .= sprintf(
                '%s <|-- %s%s',
                str_replace('\\', '.', $longName),
                str_replace('\\', '.', $model->getName()),
                $eol
            );
        }

        // prints interfaces (if exists)
        foreach ($model->getInterfaceNames() as $argument) {
            try {
                $interface   = $this->getClassModel($argument);
                $longName    = $interface->getName();
                $undeclared  = false;

            } catch (\Exception $e) {
                // interface is undeclared in data source
                $parts       = explode('\\', $argument);
                $shortName   = array_pop($parts);
                $longName    = $argument;
                $ns          = implode('.', $parts);
                $undeclared  = true;
            }

            if ($undeclared && !in_array($argument, $this->undeclaredObjects)) {
                if (empty($ns)) {
                    $ns = self::GLOBAL_NAMESPACE;
                }
                $this->undeclaredObjects[] = $argument;
                $this->undeclared .= sprintf(
                    '%snamespace %s %s {%s',
                    $eol,
                    $ns,
                    $undeclared ? '#EB937F' : '',
                    $eol
                );
                $this->undeclared .= sprintf(
                    'interface %s%s',
                    $shortName,
                    $eol
                );
                $this->undeclared .= sprintf('}%s', $eol);
            }

            $diag .= sprintf(
                '%s <|.. %s%s',
                str_replace('\\', '.', $longName),
                str_replace('\\', '.', $model->getName()),
                $eol
            );
        }

        // print class declaration
        if ($model->isAbstract()) {
            $type = 'abstract';
        } elseif ($model->isInterface()) {
            $type = 'interface';
        } else {
            $type = 'class';
        }

        $diag .= sprintf(
            '%s %s {%s',
            $type,
            $model->getShortName(),
            $eol
        );

        $constants  = $model->getConstants();
        $properties = $model->getProperties();
        $methods    = $model->getMethods();

        // prints class constants
        foreach ($model->getConstants() as $name => $value) {
            $diag .= sprintf(
                '    %s%s%s',
                '+',
                $name,
                $eol
            );
        }
        if (count($constants) && count($properties)) {
            // print separator between constants and properties
            $diag .= '    ..' . $eol;
        }

        // prints class properties
        foreach ($properties as $property) {
            if ($property->isPrivate()) {
                $visibility = '-';
            } elseif ($property->isProtected()) {
                $visibility = '#';
            } else {
                $visibility = '+';
            }

            $diag .= sprintf(
                '    %s%s%s',
                $visibility,
                $property->getName(),
                $eol
            );
        }
        if (count($methods)
            && (count($properties) || count($constants))
        ) {
            // print separator between properties and methods or constants and methods
            $diag .= '    --' . $eol;
        }

        // prints class methods
        foreach ($methods as $method) {

            if ($method->isPrivate()) {
                $visibility = '-';
            } elseif ($method->isProtected()) {
                $visibility = '#';
            } else {
                $visibility = '+';
            }

            if ($method->isStatic()) {
                $modifier = '{static} ';
            } elseif ($method->isAbstract()) {
                $modifier = '{abstract} ';
            } else {
                $modifier = '';
            }

            $diag .= sprintf(
                '    %s%s%s()%s',
                $modifier,
                $visibility,
                $method->getShortName(),
                $eol
            );
        }

        // end of class
        $diag .= sprintf('}%s', $eol);

        return $diag;
    }
}