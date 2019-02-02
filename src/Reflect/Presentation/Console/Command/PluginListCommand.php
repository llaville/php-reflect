<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console\Command;

use Bartlett\Reflect\Application\Command\PluginListCommand as AppPluginListCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List all plugins installed.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class PluginListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('bartlett:plugin:list')
            ->setDescription('List all plugins installed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new AppPluginListCommand(
            $this->getApplication()->getVendorDir(),
            $this->getApplication()->getBaseAnalyserDir(),
            $this->getApplication()->getJsonConfigFilename(),
            $input->getOption('no-plugins')
        );

        $response = $this->commandBus->handle($command);

        $this->doWrite($output, $response);
    }

    protected function doWrite(OutputInterface $output, array $response): void
    {
        if ($this->doRenderOutput($output, $response)) {
            return;
        }

        if (empty($response)) {
            $output->writeln('<info>No plugin installed</info>');
            return;
        }

        $headers = ['Plugin Class', 'Events Subscribed'];
        $rows    = [];

        foreach ($response as $pluginClass => $events) {
            $first  = true;
            foreach ($events as $event) {
                if (!$first) {
                    $rows[] = ['', $event];
                } else {
                    $rows[] = [$pluginClass, $event];
                    $first  = false;
                }
            }
        }
        $this->tableHelper($output, $headers, $rows);
    }
}
