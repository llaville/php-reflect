<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

class PluginListCommand extends Command
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
        $var = $this->getApplication()->getJsonConfigFile();

        if (!is_array($var)) {
            throw new \Exception(
                'The json configuration file has an invalid format'
            );
        }

        if (!isset($var['plugins'])) {
            $fmt = $this->getApplication()->getHelperSet()->get('formatter');

            $output->writeln(
                $fmt->formatBlock(
                    array('[Json Configuration]', 'No plugins detected.'),
                    'error'
                )
            );
            return;
        }

        if (is_array($var['plugins'])) {
            $plugins = $var['plugins'];
        } else {
            $plugins = array($var['plugins']);
        }

        $headers = array('Plugin Name', 'Plugin Class', 'Events Subscribed');
        $rows = array();
        foreach ($plugins as $plugin) {
            $classPlugin = $plugin['class'];
            $events = $classPlugin::getSubscribedEvents();

            foreach ($events as $event => $function) {
                if (count($rows)) {
                    $rows[] = array('', '', $event);
                } else {
                    $rows[] = array($plugin['name'], $plugin['class'], $event);
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
