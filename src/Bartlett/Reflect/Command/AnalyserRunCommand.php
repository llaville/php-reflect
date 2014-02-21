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
use Bartlett\Reflect\Analyser\StructureAnalyser;
use Bartlett\Reflect\Printer\Text;

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
            ->addOption(
                'alias',
                null,
                InputOption::VALUE_NONE,
                'If set, the source refers to its alias'
            )
            ->addOption(
                'php',
                null,
                InputOption::VALUE_REQUIRED,
                'Filter results on PHP version'
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
        $source = trim($input->getArgument('source'));
        if ($input->getOption('alias')) {
            $alias = $source;
        } else {
            $alias = false;
        }

        $php = $input->getOption('php');
        if ($php) {
            if (!preg_match(
                '/^\s*(==|!=|[<>]=?)?\s*(.*)$/',
                $php,
                $matches
            )) {
                throw new \InvalidArgumentException(
                    sprintf('Don\'t understand "%s" as a version number.', $php)
                );
            }
            $php = array($matches[1], $matches[2]);
        }

        $var = $this->getApplication()->getJsonConfigFile();

        if (!is_array($var)
            || !isset($var['source-providers'])
        ) {
            throw new \Exception(
                'The compatinfo.json file has an invalid format'
            );
        }

        if (is_array($var['source-providers'])) {
            $providers = $var['source-providers'];
        } else {
            $providers = array($var['source-providers']);
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

            $analyser = new AnalyserPlugin(
                new StructureAnalyser
            );
            $reflect->addSubscriber($analyser);

            if ($output->isVerbose()) {
                $reflect->getEventDispatcher()->addListener(
                    'reflect.progress',
                    function (GenericEvent $e) use ($progress) {
                        if ($progress instanceof ProgressHelper) {
                            $progress->advance();
                        }
                    }
                );
            }

            $reflect->parse();

            if ($progress instanceof ProgressHelper) {
                $progress->clear();
                $progress->finish();
            }

            $metrics = $analyser->getMetrics();
            if (!$metrics) {
                // No reports printed if there are no metrics.
                return;
            }

            $count = $metrics['DataSource'];
            $count['constants'] = $count['classConstants'] + $count['globalConstants'];

            $lines = array();

            if ($count['directories'] > 0) {
                $lines['dataSourceAnalysed'] = array(
                    '<info>Data Source Analysed</info>%s',
                    array(PHP_EOL)
                );
                $lines['directories'] = array(
                    'Directories                                 %10d',
                    array($count['directories'])
                );
                $lines['files'] = array(
                    'Files                                       %10d',
                    array($count['files'])
                );
            }

            if ($count['testClasses'] > 0) {
                $lines['testClasses'] = array(
                    '  Classes                                   %10d',
                    array($count['testClasses'])
                );
                $lines['testMethods'] = array(
                    '  Methods                                   %10d',
                    array($count['testMethods'])
                );
            }

            $lines['structure'] = array(
                '%sStructure',
                array(PHP_EOL)
            );
            $lines['namespaces'] = array(
                '  Namespaces                                %10d',
                array($count['namespaces'])
            );
            $lines['interfaces'] = array(
                '  Interfaces                                %10d',
                array($count['interfaces'])
            );
            $lines['traits'] = array(
                '  Traits                                    %10d',
                array($count['traits'])
            );

            $lines['classes'] = array(
                '  Classes                                   %10d',
                array($count['classes'])
            );
            $lines['abstractClasses'] = array(
                '    Abstract Classes                        %10d (%.2f%%)',
                array(
                    $count['abstractClasses'],
                    $count['classes'] > 0 ? ($count['abstractClasses'] / $count['classes']) * 100 : 0,
                )
            );
            $lines['concreteClasses'] = array(
                '    Concrete Classes                        %10d (%.2f%%)',
                array(
                    $count['concreteClasses'],
                    $count['classes'] > 0 ? ($count['concreteClasses'] / $count['classes']) * 100 : 0,
                )
            );

            $lines['methods'] = array(
                '  Methods                                   %10d',
                array($count['methods'])
            );
            $lines['methodsScope'] = array(
                '    Scope',
                array()
            );
            $lines['nonStaticMethods'] = array(
                '      Non-Static Methods                    %10d (%.2f%%)',
                array(
                    $count['nonStaticMethods'],
                    $count['methods'] > 0 ? ($count['nonStaticMethods'] / $count['methods']) * 100 : 0,
                )
            );
            $lines['staticMethods'] = array(
                '      Static Methods                        %10d (%.2f%%)',
                array(
                    $count['staticMethods'],
                    $count['methods'] > 0 ? ($count['staticMethods'] / $count['methods']) * 100 : 0,
                )
            );
            $lines['methodsVisibility'] = array(
                '    Visibility',
                array()
            );
            $lines['publicMethods'] = array(
                '      Public Method                         %10d (%.2f%%)',
                array(
                    $count['publicMethods'],
                    $count['methods'] > 0 ? ($count['publicMethods'] / $count['methods']) * 100 : 0,
                )
            );
            $lines['protectedMethods'] = array(
                '      Protected Method                      %10d (%.2f%%)',
                array(
                    $count['protectedMethods'],
                    $count['methods'] > 0 ? ($count['protectedMethods'] / $count['methods']) * 100 : 0,
                )
            );
            $lines['privateMethods'] = array(
                '      Private Method                        %10d (%.2f%%)',
                array(
                    $count['privateMethods'],
                    $count['methods'] > 0 ? ($count['privateMethods'] / $count['methods']) * 100 : 0,
                )
            );

            $lines['functions'] = array(
                '  Functions                                 %10d',
                array($count['functions'])
            );
            $lines['namedFunctions'] = array(
                '    Named Functions                         %10d (%.2f%%)',
                array(
                    $count['namedFunctions'],
                    $count['functions'] > 0 ? ($count['namedFunctions'] / $count['functions']) * 100 : 0,
                )
            );
            $lines['anonymousFunctions'] = array(
                '    Anonymous Functions                     %10d (%.2f%%)',
                array(
                    $count['anonymousFunctions'],
                    $count['functions'] > 0 ? ($count['anonymousFunctions'] / $count['functions']) * 100 : 0,
                )
            );

            $lines['constants'] = array(
                '  Constants                                 %10d',
                array($count['constants'])
            );
            $lines['globalConstants'] = array(
                '    Global Constants                        %10d (%.2f%%)',
                array(
                    $count['globalConstants'],
                    $count['constants'] > 0 ? ($count['globalConstants'] / $count['constants']) * 100 : 0,
                )
            );
            $lines['classConstants'] = array(
                '    Class Constants                         %10d (%.2f%%)',
                array(
                    $count['classConstants'],
                    $count['constants'] > 0 ? ($count['classConstants'] / $count['constants']) * 100 : 0,
                )
            );

            $printer = new Text;
            $printer->write($output, $lines);
            return;
        }

        throw new \Exception(
            'None data source matching'
        );
    }
}
