<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

class ProviderListCommand extends ProviderCommand
{
    protected function configure()
    {
        $this
            ->setName('provider:list')
            ->setDescription('List all data source providers.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $var = parent::execute($input, $output);

        if (is_int($var)) {
            // json config file is missing or invalid
            return $var;
        }

        $rows = array();
        foreach ($var['source-providers'] as $provider) {
            if ($this->findProvider($provider, false, false) === false) {
                continue;
            }
            $rows[] = array(
                $this->source[0],
                $this->source[1],
                sprintf('%6d', $this->finder->count())
            );
        }

        $this->getApplication()
            ->getHelperSet()
            ->get('table')
            ->setLayout(TableHelper::LAYOUT_COMPACT)
            ->setHeaders(
                array('Source', 'Alias', 'Files')
            )
            ->setRows($rows)
            ->render($output)
        ;
    }
}
