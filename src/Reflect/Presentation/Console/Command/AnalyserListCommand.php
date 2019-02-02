<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console\Command;

use Bartlett\Reflect\Application\Command\AnalyserListCommand as AppAnalyserListCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List all analysers available.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class AnalyserListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('bartlett:analyser:list')
            ->setDescription('List all analysers available.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new AppAnalyserListCommand();

        $response = $this->commandBus->handle($command);

        $this->doWrite($output, $response);
    }

    protected function doWrite(OutputInterface $output, array $response): void
    {
        if ($this->doRenderOutput($output, $response)) {
            return;
        }

        if (empty($response)) {
            $output->writeln('<error>No analysers detected.</error>');
        } else {
            $headers = ['Analyser Name', 'Analyser Class'];
            $rows    = [];

            foreach ($response as $name => $class) {
                $rows[] = [$name, $class];
            }

            $this->tableHelper($output, $headers, $rows);
        }
    }
}
