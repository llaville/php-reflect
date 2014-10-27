<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\EventDispatcher\GenericEvent;

use Bartlett\Reflect;
use Bartlett\Reflect\Command\ProviderCommand;
use Bartlett\Reflect\ProviderManager;
use Bartlett\Reflect\Provider\SymfonyFinderProvider;
use Bartlett\Reflect\Plugin\PlantUML\PlantUMLPlugin;

class PlantUMLRunCommand extends ProviderCommand
{
    protected function configure()
    {
        $this
            ->setName('plantUML:run')
            ->setDescription('Analyse a data source and build PlantUML diagrams.')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the data source or its alias'
            )
            ->addOption(
                'package',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, print a package UML diagram.',
                '+global'
            )
            ->addOption(
                'class',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, print a class UML diagram (qualified name).'
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = trim($input->getArgument('source'));
        if ($input->getOption('alias')) {
            $alias = $source;
        } else {
            $alias = false;
        }

        $var = $this->getApplication()->getEnv()->getJsonConfigFile();

        if (!is_array($var)
            || !isset($var['source-providers'])
        ) {
            throw new \Exception(
                'The json configuration file has an invalid format'
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
            $pm->set($this->source[0], new SymfonyFinderProvider($this->finder));

            $reflect = new Reflect;
            $reflect->setProviderManager($pm);

            $plugin  = new PlantUMLPlugin();
            $reflect->addSubscriber($plugin);

            if ($output->isVerbose()) {
                $reflect->getEventDispatcher()->addListener(
                    'reflect.progress',
                    function (GenericEvent $e) use($progress) {
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

            if ($class = $input->getOption('class')) {
                $text = $plugin->getClassDiagram($class);
                $output->writeln('<info>PlantUML class diagram</info>');

            } elseif ($package = $input->getOption('package')) {
                $text = $plugin->getPackageDiagram($package);
                $output->writeln('<info>PlantUML package diagram</info>');
            }
            $output->writeln($text);
            return;
        }

        throw new \Exception(
            'None data source matching'
        );
    }
}
