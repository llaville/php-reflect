<?php
namespace Bartlett\Reflect\Examples;

/**
 * Concrete example that is able to make PlantUML diagrams.
 *
 * @author Laurent Laville
 * @link plantuml.sourceforge.net/
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

use Bartlett\Reflect;
use Bartlett\Reflect\Visitor\AbstractVisitor;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Symfony\Component\Finder\Finder;

class PlantUMLBuilder extends AbstractVisitor
{
    protected $packages;
    protected $pkgid;
    protected $clsid;
    protected $asciidoc;

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

    public function visitClassModel($class)
    {
        if ($class->isAbstract()) {
            $interface = 'abstract';
        } elseif ($class->isInterface()) {
            $interface = 'interface';
        } else {
            $interface = 'class';
        }

        $this->clsid = md5($class->getName());

        $this->packages[$this->pkgid]['classes'][$this->clsid] = array(
            'type'    => $interface,
            'name'    => $class->getShortName(),
            'methods' => array(),
        );

        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }
    }

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
    {}


    public function visitFunctionModel($function)
    {
    }

    public function visitIncludeModel($include)
    {
    }

    public function visitDependencyModel($dependency)
    {
    }

    public function __construct($reflect, $asciidoc_option = false)
    {
        $this->packages = array();
        $this->asciidoc = $asciidoc_option;

        // explore elements results of data source parsed
        foreach ($reflect->getPackages() as $package) {
            $package->accept($this);
        }
    }

    public function getPackageDiagram()
    {
        $eol  = "\n";
        $diag = '';

        // produce plantuml diagram
        foreach ($this->packages as $packageValues) {

            if ($this->asciidoc) {
                $diag .= '["plantuml"]' . $eol;
                $diag .= '---------------------------------------------------------------------' . $eol;
            }

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

            if ($this->asciidoc) {
                $diag .= '---------------------------------------------------------------------' . $eol;
            }
        }

        return $diag;
    }

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
        $diag = '';

        // produce plantuml diagram

        if ($this->asciidoc) {
            $diag .= '["plantuml"]' . $eol;
            $diag .= '---------------------------------------------------------------------' . $eol;
        }

        $diag .= sprintf(
            '%s %s{%s',
            $classValues['type'],
            $classValues['name'],
            $eol
        );

        foreach ($classValues['methods'] as $method) {
            $diag .= sprintf(
                '    %s%s()%s',
                $method['visibility'],
                $method['name'],
                $eol
            );
        }

        $diag .= sprintf('}%s', $eol);

        if ($this->asciidoc) {
            $diag .= '---------------------------------------------------------------------' . $eol;
        }

        return $diag;
    }
}

$dirs = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src';

$finder = new Finder();
$finder->files()
    ->name('*.php')
    ->in($dirs);

$provider = new SymfonyFinderProvider($finder);

$pm = new ProviderManager;
$pm->set('ReflectSource', $provider);

$reflect = new Reflect;
$reflect->setProviderManager($pm);
$reflect->parse();

$plantuml = new PlantUMLBuilder($reflect, true);

$diag = $plantuml->getPackageDiagram();

$fp = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'packageDiagram.plantuml', 'w+');
fwrite($fp, $diag);
fclose($fp);

$diag = $plantuml->getClassDiagram('Bartlett\Reflect\Builder');

$fp = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'classDiagram.plantuml', 'w+');
fwrite($fp, $diag);
fclose($fp);
