<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

class ProviderShowCommand extends ProviderCommand
{
    protected function configure()
    {
        $this
            ->setName('provider:show')
            ->setDescription('Show list of files in a data source.')
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

        $var = $this->getApplication()->getJsonConfigFile();

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

            $rows   = array();
            $rows[] = array('<info>Source</info>', '<info>Files</info>','');
            $rows[] = array($this->source[0], $this->finder->count(), '');
            $rows[] = array('<info>Relative Path Name</info>', '<info>Date</info>', '<info>Size</info>');

            foreach ($this->finder as $file) {
                $rows[] = array(
                    $file->getRelativePathname(),
                    date(\DateTime::W3C, $file->getMTime()),
                    sprintf('%7d', $file->getSize())
                );
            }

            $this->getApplication()
                ->getHelperSet()
                ->get('table')
                ->setLayout(TableHelper::LAYOUT_COMPACT)
                ->setRows($rows)
                ->render($output)
            ;
            return;
        }

        throw new \Exception(
            'None data source matching'
        );
    }
}
