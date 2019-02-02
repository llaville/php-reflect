<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console\Command;

use Bartlett\Reflect\Application\Command\ReflectionClassCommand as AppReflectionClassCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Reports information about a user class present in a data source.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class ReflectionClassCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('bartlett:reflection:class')
            ->setDescription('Reports information about a user class present in a data source.')
            ->addArgument(
                'class',
                InputArgument::REQUIRED,
                'Name of the class to inspect'
            )
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the data source or its alias'
            )
            ->addOption('alias', null, null, 'If set, the source refers to its alias')
            ->addOption('format', null, InputOption::VALUE_OPTIONAL, 'To ouput results in other formats.', 'txt')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new AppReflectionClassCommand(
            $input->getArgument('class'),
            $input->getArgument('source'),
            $input->getOption('format')
        );

        $response = $this->commandBus->handle($command);

        $this->doWrite($output, [$response]);
    }

    protected function doWrite(OutputInterface $output, array $response): void
    {
        if ($this->doRenderOutput($output, $response)) {
            return;
        }

        $output->writeln((string) $response[0]);
    }
}
