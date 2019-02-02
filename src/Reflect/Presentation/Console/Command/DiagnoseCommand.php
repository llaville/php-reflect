<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console\Command;

use Bartlett\Reflect\Application\Command\DiagnoseCommand as AppDiagnoseCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ZendDiagnostics\Result\FailureInterface;
use ZendDiagnostics\Result\SuccessInterface;

/**
 * Diagnoses the system to identify common errors.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class DiagnoseCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('bartlett:diagnose')
            ->setDescription('Diagnoses the system to identify common errors.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new AppDiagnoseCommand();

        $response = $this->commandBus->handle($command);

        $this->doWrite($output, $response);
    }

    protected function doWrite(OutputInterface $output, array $response): void
    {
        if ($this->doRenderOutput($output, $response)) {
            return;
        }

        $results = $response['results'] ?? [];

        foreach ($results as $check) {
            if ($results[$check] instanceof FailureInterface) {
                $output->writeln('> <error>KO</error> - ' . $results[$check]->getMessage());
            } elseif ($results[$check] instanceof SuccessInterface) {
                $output->writeln('> <info>OK</info> - ' . $results[$check]->getMessage());
            }
        }

        $checks = $response['checks'] ?? [];

        $output->writeln('');
        $output->writeln(sprintf('%d diagnostic tests', count($checks)));
    }
}
