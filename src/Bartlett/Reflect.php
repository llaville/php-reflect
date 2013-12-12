<?php

namespace Bartlett;

use Bartlett\Reflect\Event\AbstractDispatcher;
use Bartlett\Reflect\ManagerInterface;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Token;
use Bartlett\Reflect\Provider;
use Bartlett\Reflect\Parser\ParserInterface;
use Bartlett\Reflect\Parser\DefaultParser;
use Bartlett\Reflect\Builder;
use Bartlett\Reflect\Tokenizer\DefaultTokenizer;
use Bartlett\Reflect\Filter\FilenameFilter;

class Reflect extends AbstractDispatcher implements ManagerInterface
{
    protected $pm;
    protected $parsers;
    protected $tokenizer;
    protected $builder;

    /**
     * @var array
     */
    protected $options;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->parsers = new \SplDoublyLinkedList;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function pushParser(ParserInterface $parser)
    {
        $this->parsers->push($parser);
    }

    /**
     * {@inheritdoc}
     */
    public function popParser()
    {
        if ($this->parsers->isEmpty()) {
            throw new \LogicException('You tried to pop from an empty parser stack.');
        }
        return $this->parsers->pop();
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderManager()
    {
        if (!isset($this->pm)) {
            $this->pm = new ProviderManager;
        }
        return $this->pm;
    }

    /**
     * {@inheritdoc}
     */
    public function setProviderManager(ProviderManager $manager)
    {
        $this->pm = $manager;
    }

    /**
     *
     * @param array $providers (optional) Data source providers to parse at this runtime.
     *                         All providers defined in Provider Manager by default.
     *
     * @return array List of items parsed from the data source.
     */
    public function parse(array $providers = null)
    {
        $this->builder   = new Builder;
        $this->tokenizer = new DefaultTokenizer;

        if ($this->parsers->isEmpty()) {
            $parser = new DefaultParser($this->builder);
            $this->pushParser($parser);
        }

        if (empty($providers)) {
            $providers = array_keys(
                $this->getProviderManager()->all()
            );
        }

        foreach ($this->getProviderManager()->all() as $alias => $provider) {
            if (!in_array($alias, $providers)) {
                continue;
            }
            // creates the data model of sources referenced by the $alias name
            foreach ($provider as $uri => $file) {
                $event = $this->dispatch(
                    'reflect.progress',
                    array(
                        'source' => $alias,
                        'filename' => $file->getPathname()
                    )
                );
                if (isset($event['notModified'])) {
                    // uses cached response
                    $this->builder->buildFromCache($event['notModified']);
                } else {
                    // live request
                    $this->parseFile($file);

                    foreach ($this->builder->getPackages() as $package) {
                        $iterator = new FilenameFilter(
                            $package->getIterator(),
                            $file->getPathname()
                        );

                        if (iterator_count($iterator) > 0) {
                            // end of parsing the file, and sends results to observers
                            $this->dispatch(
                                'reflect.success',
                                array(
                                    'source'   => $alias,
                                    'filename' => $file->getPathname(),
                                    'package'  => $package->getName(),
                                    'data'     => iterator_to_array($iterator)
                                )
                            );
                        }
                    }
                }
            }
            // end of parsing the data source provider
            $this->dispatch('reflect.complete', array('source' => $alias));
        }
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
     * Gets informations about all traits.
     *
     * @return array ClassModel Map objects reflecting each trait.
     */
    public function getTraits()
    {
        return $this->builder->getTraits();
    }

    /**
     * Gets informations about all interfaces.
     *
     * @return array ClassModel Map objects reflecting each interface.
     */
    public function getInterfaces()
    {
        return $this->builder->getInterfaces();
    }

    /**
     * Gets informations about all classes.
     *
     * @return array ClassModel Map objects reflecting each class.
     */
    public function getClasses()
    {
        return $this->builder->getClasses();
    }

    /**
     * Gets informations about all functions.
     *
     * @return array FunctionModel Map objects reflecting each function.
     */
    public function getFunctions()
    {
        return $this->builder->getFunctions();
    }

    /**
     * Gets informations about all constants.
     *
     * @return array ConstantModel Map objects reflecting each global constant.
     */
    public function getConstants()
    {
        return $this->builder->getConstants();
    }

    /**
     * Gets informations about all includes.
     *
     * @return array IncludeModel Map objects reflecting each include.
     */
    public function getIncludes()
    {
        return $this->builder->getIncludes();
    }

    /**
     * Parse the contents of a single file
     *
     * @param string $file
     *
     * @return void
     */
    private function parseFile($file)
    {
        $this->tokenizer->setSourceFile($file);

        $aliases          = array();
        $namespace        = false;
        $namespaceEndLine = false;
        $class            = false;
        $classEndLine     = false;
        $interface        = false;
        $interfaceEndLine = false;
        $trait            = false;
        $traitEndLine     = false;
        $function         = false;
        $functionEndLine  = false;

        $tokenStack = $this->tokenizer->getTokens();

        while ($tokenStack->valid()) {
            $tokenStack->next();

            $token = $tokenStack->current();

            if ('T_HALT_COMPILER' == $token[0]) {
                break;
            }

            $tokenName  = $token[0];
            $text       = $token[1];
            $line       = $token[2];

            if ($tokenName == 'T_STRING') {
                // make tokens forward compatible
                $id = $tokenStack->key();

                // since PHP 5.3
                if (strcasecmp($text, '__dir__') == 0) {
                    $tokenName = 'T_DIR';
                } elseif (strcasecmp($text, '__namespace__') == 0) {
                    $tokenName = 'T_NS_C';
                } elseif (strcasecmp($text, 'namespace') == 0
                    && $namespace === false
                    && $tokenStack[$id - 1][0] != 'T_OBJECT_OPERATOR'
                ) {
                    $tokenName = 'T_NAMESPACE';
                } elseif (strcasecmp($text, 'goto') == 0) {
                    $tokenName = 'T_GOTO';

                // since PHP 5.4
                } elseif (strcasecmp($text, '__trait__') == 0) {
                    $tokenName = 'T_TRAIT_C';
                } elseif (strcasecmp($text, 'trait') == 0
                    && $trait === false
                ) {
                    $tokenName = 'T_TRAIT';
                } elseif (strcasecmp($text, 'insteadof') == 0) {
                    $tokenName = 'T_INSTEADOF';
                } elseif (strcasecmp($text, 'callable') == 0) {
                    $tokenName = 'T_CALLABLE';

                // since PHP 5.5
                } elseif (strcasecmp($text, 'finally') == 0) {
                    $tokenName = 'T_FINALLY';
                } elseif (strcasecmp($text, 'yield') == 0) {
                    $tokenName = 'T_YIELD';
                }
            }

            $context = array(
                'aliases'   => $aliases,
                'namespace' => $namespace,
                'class'     => $class,
                'interface' => $interface,
                'trait'     => $trait,
                'function'  => $function,
            );

            if ('T_CLOSE_CURLY' === $tokenName) {
                if ($namespaceEndLine !== false
                    && $namespaceEndLine == $line
                ) {
                    $namespace        = false;
                    $namespaceEndLine = false;
                }
                if ($classEndLine !== false
                    && $classEndLine == $line
                ) {
                    $class        = false;
                    $classEndLine = false;
                }
                if ($interfaceEndLine !== false
                    && $interfaceEndLine == $line
                ) {
                    $interface        = false;
                    $interfaceEndLine = false;
                }
                if ($traitEndLine !== false
                    && $traitEndLine == $line
                ) {
                    $trait        = false;
                    $traitEndLine = false;
                }
                if ($functionEndLine !== false
                    && $functionEndLine == $line
                ) {
                    $function        = false;
                    $functionEndLine = false;
                }

            } else {
                $token   = false;
                $request = array(
                    'context'  => $context,
                    'tokens'   => $tokenStack,
                    'filename' => $file->getPathname(),
                );

                foreach ($this->parsers as $parser) {
                    $resp = $parser->handle($request);

                    if ($resp === false) {
                        $this->dispatch(
                            'parser.error',
                            array('request' => $request, 'parser' => $parser)
                        );
                    } else {
                        $this->dispatch(
                            'parser.success',
                            array('request' => $request, 'response' => $resp)
                        );
                    }

                    if ($token === false && $resp !== false) {
                        // backup token object on first handled request
                        $token = $resp;
                    }
                }
                $this->dispatch(
                    'parser.complete',
                    array('request' => $request, 'response' => $token)
                );

                if ($token !== false) {
                    if ($tokenName == 'T_NAMESPACE') {
                        $namespace        = $token->getName();
                        $namespaceEndLine = $token->getEndLine();

                    } elseif ($tokenName == 'T_USE') {
                        if ($class !== false) {
                            // warning: don't set $trait value
                            $traitEndLine = $token->getEndLine();
                        } else {
                            // namespaces imported
                            $aliases[ $token->getAlias() ] = $token->getName(false);
                        }

                    } elseif ($tokenName == 'T_TRAIT') {
                        $trait        = $token->getName();
                        $traitEndLine = $token->getEndLine();

                    } elseif ($tokenName == 'T_INTERFACE') {
                        $interface        = $token->getName();
                        $interfaceEndLine = $token->getEndLine();

                    } elseif ($tokenName == 'T_CLASS') {
                        $class        = $token->getName();
                        $classEndLine = $token->getEndLine();

                    } elseif ($tokenName == 'T_FUNCTION') {
                        // function or method
                        $function        = $token->getName();
                        $functionEndLine = $token->getEndLine();
                    }
                }
            }
        }
    }
}
