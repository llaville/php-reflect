<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console\Command;

use Bartlett\Reflect\Application\Command\ConfigValidateCommand as AppConfigValidateCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Validates structure of the JSON configuration file.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class ConfigValidateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('bartlett:config:validate')
            ->setDescription('Validates a JSON configuration file.')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'Path to phpreflect.json file',
                getcwd() . DIRECTORY_SEPARATOR . getenv('BARTLETTRC')
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('file');

        $command = new AppConfigValidateCommand($filename);

        $response = $this->commandBus->handle($command);

        $this->doWrite($output, $response, $filename);
    }

    protected function doWrite(OutputInterface $output, array $response, string $filename): void
    {
        if ($this->doRenderOutput($output, $response)) {
            return;
        }

        $output->writeln(
            sprintf(
                '<info>%s</info> config file is valid',
                $filename
            )
        );
    }
}
