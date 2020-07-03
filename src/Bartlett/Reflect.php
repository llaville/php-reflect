<?php
/**
 * Reflect
 * Reverse-engineer classes, interfaces, traits, functions, constants, namespaces
 * and more.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett;

use Bartlett\Reflect\Event\AbstractDispatcher;
use Bartlett\Reflect\Event\CompleteEvent;
use Bartlett\Reflect\Event\ErrorEvent;
use Bartlett\Reflect\Event\ProgressEvent;
use Bartlett\Reflect\Event\SuccessEvent;
use Bartlett\Reflect\Visitor\VisitorInterface;

use PhpParser\ErrorHandler\Collecting;
use PhpParser\Lexer\Emulative;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitor;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Iterator\CustomFilterIterator;

/**
 * Reflect analyse your source code with the tokenizer extension.
 *
 * All data sources (archive, local or remote script) are parsable.
 * You can cache or logs results and even more. API is extensible simply.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 2.0.0RC1
 */
class Reflect extends AbstractDispatcher
{
    private $analysers;
    private $dataSourceId;

    /**
     * Creates a new instance of the Reflect engine
     */
    public function __construct()
    {
        $this->analysers = array();
    }

    /**
     * Adds a new analyser to get specific metrics
     *
     * @param NodeVisitor $analyser Analyser instance
     *
     * @return self for fluent interface
     */
    public function addAnalyser(NodeVisitor $analyser)
    {
        $analyser->setSubject($this);
        $this->analysers[] = $analyser;
        return $this;
    }

    /**
     * Gets the list of active analysers.
     *
     * @return array
     */
    public function getAnalysers()
    {
        return $this->analysers;
    }

    /**
     * Set the data source identifier
     *
     * @param string $id Identitier to use for the data source
     *
     * @return self for fluent interface
     */
    public function setDataSourceId($id)
    {
        $this->dataSourceId = $id;
        return $this;
    }

    /**
     * Gets identifier of the current data source
     *
     * @return string
     */
    public function getDataSourceId()
    {
        return $this->dataSourceId;
    }

    /**
     * Analyse a data source and return all analyser metrics.
     *
     * @param Finder $finder A data source finder
     *
     * @return array|boolean array of all analysers metrics, or FALSE if no parse occured
     */
    public function parse(Finder $finder)
    {
        $metrics = array();

        if (empty($this->analysers)) {
            return false;
        }

        $lexer = new Emulative(array(
            'usedAttributes' => array(
                'comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos'
            )
        ));
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver);
        $conditionalCode = false;

        // attach all analysers selected
        foreach ($this->analysers as $analyser) {
            if ('compatibility' == $analyser->getShortName()) {
                $conditionalCode = true;
            }
            $traverser->addVisitor($analyser);

            if ($analyser instanceof VisitorInterface) {
                $analyser->setUpBeforeVisitor();
            }
        }

        $queue    = new \SplQueue();
        $priority = array();

        if ($conditionalCode) {
            // files to process with highest priority
            $filter = new CustomFilterIterator(
                $finder->getIterator(),
                array(
                    function (\SplFileInfo $fileinfo) {
                        $content = php_strip_whitespace($fileinfo->getPathname());
                        if (preg_match('/define\s*\(/i', $content) > 0) {
                            // must be confirmed to avoid false positive with string content
                            $tokens = token_get_all($content);

                            for ($i = 0, $max = count($tokens); $i < $max; $i++) {
                                if (is_array($tokens[$i])
                                    && $tokens[$i][0] == T_STRING
                                    && strcasecmp($tokens[$i][1], 'define') == 0
                                ) {
                                    // confirmed by token strategy
                                    return true;
                                }
                            }
                        }
                        return false;
                    }
                )
            );
            foreach ($filter as $file) {
                $path = $file->getPathname();
                $priority[] = $path;
                $queue->enqueue($file);
            }

            // just followed by this other highest priority
            $filter = new CustomFilterIterator(
                $finder->getIterator(),
                array(
                    function (\SplFileInfo $fileinfo) {
                        $content = php_strip_whitespace($fileinfo->getPathname());
                        if (preg_match('/defined\s*\(/i', $content) > 0) {
                            // must be confirmed to avoid false positive with string content
                            $tokens = token_get_all($content);

                            for ($i = 0, $max = count($tokens); $i < $max; $i++) {
                                if (is_array($tokens[$i])
                                    && $tokens[$i][0] == T_STRING
                                    && strcasecmp($tokens[$i][1], 'defined') == 0
                                ) {
                                    // confirmed by token strategy
                                    return true;
                                }
                            }
                        }
                        return false;
                    }
                )
            );
            foreach ($filter as $file) {
                $path = $file->getPathname();
                if (!in_array($path, $priority)) {
                    $priority[] = $path;
                    $queue->enqueue($file);
                }
            }

            $filter = new CustomFilterIterator(
                $finder->getIterator(),
                array(
                    function (\SplFileInfo $fileinfo) {
                        $content = php_strip_whitespace($fileinfo->getPathname());

                        $checks = array(
                            'extension_loaded',
                            'function_exists',
                            'method_exists',
                            'class_exists',
                            'interface_exists',
                            'trait_exists',
                        );
                        $patterns = array_map(
                            function ($a) {
                                return "/$a\s*\(/i";
                            },
                            $checks
                        );
                        foreach ($patterns as $regexp) {
                            if (preg_match($regexp, $content) > 0) {
                                // must be confirmed to avoid false positive with string content
                                $tokens = token_get_all($content);

                                for ($i = 0, $max = count($tokens); $i < $max; $i++) {
                                    if (is_array($tokens[$i])
                                        && $tokens[$i][0] == T_STRING
                                        && in_array(strtolower($tokens[$i][1]), $checks)
                                    ) {
                                        // confirmed by token strategy
                                        return true;
                                    }
                                }
                            }
                        }
                        return false;
                    }
                )
            );
            foreach ($filter as $file) {
                $path = $file->getPathname();
                if (!in_array($path, $priority)) {
                    $priority[] = $path;
                    $queue->enqueue($file);
                }
            }

            unset($filter);
        }

        // all other files with lowest priority
        foreach ($finder as $file) {
            if (!in_array($file->getPathname(), $priority)) {
                $queue->enqueue($file);
            }
        }

        $files = $parserErrors = array();

        // generate a data source identifier if not provided
        if (!isset($this->dataSourceId)) {
            $this->dataSourceId = sha1(serialize($finder->getIterator()));
        }

        // analyse each file of the data source
        while (!$queue->isEmpty()) {
            $file = $queue->dequeue();

            $event = $this->dispatch(
                new ProgressEvent(
                    $this,
                    array(
                        'source'   => $this->dataSourceId,
                        'file'     => $file,
                    )
                )
            );
            $files[] = $file->getPathname();

            if (isset($event['notModified'])) {
                $tokens = @token_get_all(
                    file_get_contents($file->getPathname())
                );
                // uses cached response (AST built by PHP-Parser)
                $stmts = $event['notModified'];
            } else {
                // live request
                $errorHandler = new Collecting();

                $stmts = $parser->parse(
                    file_get_contents($file->getPathname()),
                    $errorHandler
                );

                if ($errorHandler->hasErrors()) {
                    foreach ($errorHandler->getErrors() as $e) {
                        $this->dispatch(
                            new ErrorEvent(
                                $this,
                                array(
                                    'source' => $this->dataSourceId,
                                    'file'   => $file,
                                    'error'  => $e->getMessage()
                                )
                            )
                        );
                        $parserErrors[$file->getPathname()] = $e->getMessage();
                    }
                    continue; // skip to next file of the data source
                }

                $tokens = $lexer->getTokens();
            }

            // update context for each analyser selected
            foreach ($this->analysers as $analyser) {
                $analyser->setTokens($tokens);
                $analyser->setCurrentFile($file->getPathname());
            }

            $stmts = $traverser->traverse($stmts);

            $this->dispatch(
                new SuccessEvent(
                    $this,
                    array(
                        'source' => $this->dataSourceId,
                        'file'   => $file,
                        'ast'    => $stmts,
                    )
                )
            );
        }

        // end of parsing the data source
        $event = $this->dispatch(
            new CompleteEvent($this, array('source' => $this->dataSourceId))
        );
        if (isset($event['extra'])) {
            $metrics['extra'] = $event['extra'];
        }

        // list of files parsed
        $metrics['files'] = $files;

        // list of PHP-Parser errors
        $metrics['errors'] = $parserErrors;

        // collect metrics of each analyser selected
        foreach ($this->analysers as $analyser) {
            if ($analyser instanceof VisitorInterface) {
                $analyser->tearDownAfterVisitor();
            }

            $metrics = array_merge($metrics, (array)$analyser->getMetrics());
        }

        return $metrics;
    }
}
