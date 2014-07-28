<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\EventDispatcher\GenericEvent;

use Bartlett\Reflect;
use Bartlett\Reflect\Command\ProviderCommand;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Bartlett\Reflect\Plugin\Analyser\AnalyserPlugin;
use Bartlett\Reflect\Plugin\Cache\DefaultCacheStorage;

class AnalyserRunCommand extends ProviderCommand
{
    protected function configure()
    {
        $this
            ->setName('analyser:run')
            ->setDescription('Analyse a data source and display results.')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the data source or its alias'
            )
            ->addArgument(
                'analysers',
                InputArgument::IS_ARRAY,
                'Add one or more analyser to run at end of process (case insensitive).'
            )
            ->addOption(
                'alias',
                null,
                InputOption::VALUE_NONE,
                'If set, the source refers to its alias'
            )
            ->addOption(
                'redraw-freq',
                null,
                InputOption::VALUE_REQUIRED,
                'How many times should the progress bar be updated?',
                1
            )
        ;
    }

    /**
     *
     * @throws \InvalidArgumentException if an analyser required is not installed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $var = $this->getApplication()->getJsonConfigFile();

        if (!is_array($var)
            || !isset($var['source-providers'])
            || !isset($var['analysers'])
        ) {
            throw new \Exception(
                'The compatinfo.json file has an invalid format'
            );
        }

        $source = trim($input->getArgument('source'));
        if ($input->getOption('alias')) {
            $alias = $source;
        } else {
            $alias = false;
        }

        if (is_array($var['source-providers'])) {
            $providers = $var['source-providers'];
        } else {
            $providers = array($var['source-providers']);
        }

        if (is_array($var['plugins'])) {
            $pluginsInstalled = $var['plugins'];
        } else {
            $pluginsInstalled = array($var['plugins']);
        }

        if (is_array($var['analysers'])) {
            $analysersInstalled = $var['analysers'];
        } else {
            $analysersInstalled = array($var['analysers']);
        }

        $analysers = array();

        $analysersAsked = $input->getArgument('analysers');
        if (empty($analysersAsked)) {
            // default analyser
            $analysersAsked = array('Structure');
        }

        foreach ($analysersAsked as $analyser) {
            $found = false;
            foreach ($analysersInstalled as $analyserInstalled) {
                if (strcasecmp($analyserInstalled['name'], $analyser) === 0) {
                    // analyser installed and available
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Analyser "%s" is not installed. Checks with analyser:list command.',
                        $analyser
                    )
                );
            }
            $analysers[] = new $analyserInstalled['class'];
        }

        foreach ($providers as $provider) {
            if ($this->findProvider($provider, $source, $alias) === false) {
                continue;
            }

            if ($output->isQuiet()) {
                $progress = false;
            } else {
                $progress = $this->getApplication()
                    ->getHelperSet()
                    ->get('progress')
                ;

                if ($freq = $input->getOption('redraw-freq')) {
                    $progress->setRedrawFrequency($freq);
                }

                $max = $this->finder->count();
                $progress->start($output, $max);
            }

            $pm = new ProviderManager;
            $pm->set('DataSource', new SymfonyFinderProvider($this->finder));

            $reflect = new Reflect;
            $reflect->setProviderManager($pm);

            $plugin = new AnalyserPlugin($analysers);
            $reflect->addSubscriber($plugin);

            foreach ($pluginsInstalled as $pluginInstalled) {
                if (stripos($pluginInstalled['class'], 'cacheplugin') === false) {
                    continue;
                }
                // cache plugin found

                if (isset($pluginInstalled['options']['adapter'])) {
                    $adapterClass = $pluginInstalled['options']['adapter'];
                } else {
                    // default cache adapter
                    $adapterClass = 'DoctrineCacheAdapter';
                }
                if (strpos($adapterClass, '\\') === false) {
                    // add default namespace
                    $adapterClass = "Bartlett\\Reflect\\Cache\\" . $adapterClass;
                }
                if (!class_exists($adapterClass)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Adapter "%s" cannot be loaded.',
                            $adapterClass
                        )
                    );
                }

                if (!isset($pluginInstalled['options']['backend']['class'])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Backend is missing for %s',
                            $adapterClass
                        )
                    );
                }
                $backendClass = $pluginInstalled['options']['backend']['class'];

                if (!class_exists($backendClass)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Backend "%s" cannot be loaded.',
                            $backendClass
                        )
                    );
                }
                $rc = new \ReflectionClass($backendClass);

                if (isset($pluginInstalled['options']['backend']['args'])
                    && is_array($pluginInstalled['options']['backend']['args'])
                ) {
                    $args = $pluginInstalled['options']['backend']['args'];
                } else {
                    $args = array();
                }

                for ($a = 0, $max = count($args); $a < $max; $a++) {
                    // Expands variable from Environment on each argument
                    $count = preg_match_all("/%{([^}]*)}/", $args[$a], $reg);
                    for ($i = 0 ; $i < $count ; $i++) {
                        $val = getenv($reg[1][$i]);
                        if ($val) {
                            $args[$a] = str_replace(
                                $reg[0][$i],
                                $val,
                                $args[$a]
                            );
                        }
                    }
                }
                $backend = $rc->newInstanceArgs($args);

                $cacheAdapter = new $adapterClass($backend);

                $cache = new DefaultCacheStorage($cacheAdapter);

                $cachePlugin = new $pluginInstalled['class']($cache);
                $reflect->addSubscriber($cachePlugin);
                break;
            }

            $fmt = $this->getApplication()->getHelperSet()->get('formatter');

            if ($output->isVerbose()) {
                $reflect->getEventDispatcher()->addListener(
                    'reflect.progress',
                    function (GenericEvent $e) use ($progress, $output, $fmt) {

                        if ($output->isVeryVerbose()) {
                            static $current = 0;

                            $output->writeln(
                                $fmt->formatSection(
                                    sprintf('%05d', ++$current),
                                    $e['file']->getPathname()
                                )
                            );
                            return;
                        }
                        if ($progress instanceof ProgressHelper) {
                            $progress->advance();
                        }
                    }
                );
            }
            if ($output->isVeryVerbose()) {
                $reflect->getEventDispatcher()->addListener(
                    'reflect.error',
                    function (GenericEvent $e) use ($output, $fmt) {

                        $output->writeln(
                            $fmt->formatBlock(array($e['error']), 'error')
                        );
                    }
                );
            }

            $reflect->parse();

            if ($progress instanceof ProgressHelper) {
                $progress->clear();
                $progress->finish();
            }

            $metrics = $plugin->getMetrics();
            if (!$metrics) {
                // No reports printed if there are no metrics.
                return;
            }
            $count = $metrics['DataSource'];

            // print Data Source headers
            if ($count['directories'] > 0) {
                $text = sprintf(
                    "\n" .
                    "Directories                                 %10d\n" .
                    "Files                                       %10d\n",
                    $count['directories'],
                    $count['files']
                );
            }
            if (in_array('structure', $analysers)
                && $count['testClasses'] > 0
            ) {
                $text .= sprintf(
                    "\nTests\n" .
                    "  Classes                                   %10d\n" .
                    "  Methods                                   %10d\n",
                    $count['testClasses'],
                    $count['testMethods']
                );
            }
            $output->writeln('<info>Data Source Analysed</info>');
            $output->writeln($text);

            // print each analyser report
            foreach ($analysers as $analyser) {
                $analyser->render($output);
            }

            if (isset($cachePlugin)
                && $input->getOption('profile')
            ) {
                $stats = $cachePlugin->getStats();
                $output->writeln(
                    sprintf(
                        '%s<info>Cache: %d hits, %d misses</info>',
                        PHP_EOL,
                        $stats['hits'],
                        $stats['misses']
                    )
                );
            }
            return;
        }

        throw new \Exception(
            'None data source matching'
        );
    }
}
