<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

class AnalyserListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('analyser:list')
            ->setDescription('List all analysers installed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $var = $this->getApplication()->getJsonConfigFile();

        if (!is_array($var)) {
            throw new \Exception(
                'The json configuration file has an invalid format'
            );
        }

        if (is_array($var['analysers'])) {
            $analysers = $var['analysers'];
        } else {
            $analysers = array($var['analysers']);
        }

        $headers = array('Analyser Name', 'Analyser Class');
        $rows = array();
        foreach ($analysers as $analyser) {
            $rows[] = array($analyser['name'], $analyser['class']);
        }
        sort($rows);

        $this->getApplication()
            ->getHelperSet()
            ->get('table')
            ->setLayout(TableHelper::LAYOUT_COMPACT)
            ->setHeaders($headers)
            ->setRows($rows)
            ->render($output)
        ;
    }
}
