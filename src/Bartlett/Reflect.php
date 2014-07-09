<?php
/**
 * Reflect
 * Reverse-engineer classes, interfaces, traits, functions, constants, namespaces
 * and more.
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

namespace Bartlett;

use Bartlett\Reflect\Event\AbstractDispatcher;
use Bartlett\Reflect\ManagerInterface;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Model\PackageModel;
use Bartlett\Reflect\Builder;

use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;

/**
 * Reflect analyse your source code with the tokenizer extension.
 *
 * All data sources (archive, local or remote script) are parsable.
 * You can cache or logs results and even more. API is extensible simply.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC1
 */
class Reflect extends AbstractDispatcher implements ManagerInterface
{
    protected $pm;
    protected $files;

    /**
     * Returns an instance of the current provider manager.
     *
     * @return ProviderManager
     */
    public function getProviderManager()
    {
        if (!isset($this->pm)) {
            $this->pm = new ProviderManager;
        }
        return $this->pm;
    }

    /**
     * Defines the current provider manager.
     *
     * @param ProviderManager $manager Instance of your custom source provider
     *
     * @return void
     */
    public function setProviderManager(ProviderManager $manager)
    {
        $this->pm = $manager;
    }

    /**
     * Analyse all or part of data sources identified by the Provider Manager.
     *
     * @param array $providers (optional) Data source providers to parse at this runtime.
     *                         All providers defined in Provider Manager by default.
     *
     * @return self for fluent interface
     */
    public function parse(array $providers = null)
    {
        $this->builder = new Builder;

        $parser    = new Parser(new Lexer\Emulative);
        $traverser = new NodeTraverser;
        $traverser->addVisitor(new NameResolver);
        $traverser->addVisitor($this->builder);

        $this->files = array();

        $all = $this->getProviderManager()->all();

        if (empty($providers)) {
            $providers = array_keys($all);
        }

        foreach ($all as $alias => $provider) {
            if (!in_array($alias, $providers)) {
                continue;
            }

            // creates the data model of sources referenced by the $alias name
            foreach ($provider as $uri => $file) {
                $event = $this->dispatch(
                    'reflect.progress',
                    array(
                        'source'   => $alias,
                        'file'     => $file,
                    )
                );
                $this->files[] = $file;
                $this->builder->setCurrentFile($file->getPathname());

                if (isset($event['notModified'])) {
                    // uses cached response (AST built by PHP-Parser)
                    $traverser->traverse($event['notModified']);
                } else {
                    // live request
                    try {
                        $stmts = $parser->parse(
                            file_get_contents($file->getPathname())
                        );
                        $stmts = $traverser->traverse($stmts);

                        if ($this->getEventDispatcher()->hasListeners('reflect.success')) {
                            $this->dispatch(
                                'reflect.success',
                                array(
                                    'source'   => $alias,
                                    'file'     => $file,
                                    'ast'      => serialize($stmts)
                                )
                            );
                        }

                    } catch (\PhpParser\Error $e) {
                        if ($this->getEventDispatcher()->hasListeners('reflect.error')) {
                            $this->dispatch(
                                'reflect.error',
                                array(
                                    'source'   => $alias,
                                    'file'     => $file,
                                    'error'    => $e->getMessage()
                                )
                            );
                        }
                    }
                }
            }
            // end of parsing the data source provider
            $this->dispatch('reflect.complete', array('source' => $alias));
        }

        return $this;
    }

    /**
     * Gets informations about all packages/namespaces.
     *
     * @return array PackageModel Map objects reflecting each package/namespace.
     */
    public function getPackages()
    {
        return $this->builder->getPackages();
    }

    /**
     * Gets list of files parsed.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}
