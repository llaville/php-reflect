<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

class AnalyserListCommand extends ProviderCommand
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
        $var = parent::execute($input, $output);

        if (is_int($var)) {
            // json config file is missing or invalid
            return $var;
        }

        if (empty($var['analysers'])) {
            $fmt = $this->getApplication()->getHelperSet()->get('formatter');

            $output->writeln(
                $fmt->formatBlock(
                    array('[Json Configuration]', 'No analysers detected.'),
                    'error'
                )
            );
            return;
        }

        $headers = array('Analyser Name', 'Analyser Class');
        $rows = array();
        foreach ($var['analysers'] as $analyser) {
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
