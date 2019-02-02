<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console\Command;

use Bartlett\Reflect\Application\Command\DiagramPackageCommand as AppDiagramPackageCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates diagram about namespaces in a data source.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class DiagramPackageCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('bartlett:diagram:package')
            ->setDescription('Generates diagram about namespaces in a data source.')
            ->addArgument(
                'package',
                InputArgument::REQUIRED,
                'Namespace to inspect'
            )
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the data source or its alias'
            )
            ->addOption('alias', null, null, 'If set, the source refers to its alias')
            ->addOption('engine', null, InputOption::VALUE_OPTIONAL, 'Graphical syntax.', 'plantuml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new AppDiagramPackageCommand(
            $input->getArgument('package'),
            $input->getArgument('source'),
            $input->getOption('engine')
        );

        $response = $this->commandBus->handle($command);

        $this->doWrite($output, ['stmt' => $response]);
    }

    protected function doWrite(OutputInterface $output, array $response): void
    {
        if ($this->doRenderOutput($output, $response)) {
            return;
        }

        $output->writeln($response['stmt']);
    }
}
