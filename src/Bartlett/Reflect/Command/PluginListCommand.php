<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

class PluginListCommand extends ProviderCommand
{
    protected function configure()
    {
        $this
            ->setName('plugin:list')
            ->setDescription('List all plugins installed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $var = parent::execute($input, $output);

        if (is_int($var)) {
            // json config file is missing or invalid
            return $var;
        }

        if (empty($var['plugins'])) {
            $fmt = $this->getApplication()->getHelperSet()->get('formatter');

            $output->writeln(
                $fmt->formatBlock(
                    array('[Json Configuration]', 'No plugins detected.'),
                    'error'
                )
            );
            return;
        }

        $headers = array('Plugin Name', 'Plugin Class', 'Events Subscribed');
        $rows = array();
        foreach ($var['plugins'] as $plugin) {
            $classPlugin = $plugin['class'];
            $events = $classPlugin::getSubscribedEvents();
            $first  = true;
            foreach ($events as $event => $function) {
                if (!$first) {
                    $rows[] = array('', '', $event);
                } else {
                    $rows[] = array($plugin['name'], $plugin['class'], $event);
                    $first  = false;
                }
            }
        }

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
