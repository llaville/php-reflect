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

        if (is_array($var['analysers'])) {
            $analysersInstalled = $var['analysers'];
        } else {
            $analysersInstalled = array($var['analysers']);
        }

        $analysers = array();
        foreach ($input->getArgument('analysers') as $analyser) {
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
        if (empty($analysers)) {
            // at least, there is always this analyser to print structure
            $analysers[] = new Reflect\Analyser\StructureAnalyser;
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

            foreach ($analysers as $analyser) {
                $analyser->render($output);
            }
            return;
        }

        throw new \Exception(
            'None data source matching'
        );
    }
}
