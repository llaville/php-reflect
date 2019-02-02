<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console\Command;

use Bartlett\Reflect\Application\Command\AnalyserRunCommand as AppAnalyserRunCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Analyse a data source and display results.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class AnalyserRunCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('bartlett:analyser:run')
            ->setDescription('Analyse a data source and display results.')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the data source or its alias'
            )
            ->addArgument(
                'analysers',
                InputArgument::OPTIONAL,
                'One or more analyser to perform (case insensitive).',
                'structure'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $analysers = explode(',', $input->getArgument('analysers'));

        $command = new AppAnalyserRunCommand(
            $input->getArgument('source'),
            $analysers
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
            // No reports printed if there are no metrics.
            $output->writeln('<info>No metrics.</info>');
            return;
        }

        $output->writeln('<info>Data Source Analysed</info>');

        $directories = [];

        foreach ($response['files'] as $file) {
            $directories[] = dirname($file);
        }
        $directories = array_unique($directories);

        // print Data Source summaries
        if (count($response['files']) > 0) {
            $text = sprintf(
                "%s" .
                "Directories                                 %10d%s" .
                "Files                                       %10d%s" .
                "Errors                                      %10d%s",
                PHP_EOL,
                count($directories),
                PHP_EOL,
                count($response['files']),
                PHP_EOL,
                count($response['errors']),
                PHP_EOL
            );
            $output->writeln($text);
        }

        if (count($response['errors'])) {
            $output->writeln('<info>Errors found</info>');

            foreach ($response['errors'] as $file => $msg) {
                $text = sprintf(
                    '%s <info>></info> %s in file %s',
                    PHP_EOL,
                    $msg,
                    $file
                );
                $output->writeln($text);
            }
        }

        // print each analyser results
        foreach ($response as $analyserName => $analyserResults) {
            if (substr($analyserName, -8) !== 'Analyser') {
                continue;
            }
            $baseNamespace = str_replace(
                'Analyser\\' . basename(str_replace('\\', '/', $analyserName)),
                '',
                $analyserName
            );
            $outputFormatter = $baseNamespace . 'Console\Formatter\\' .
                substr(basename(str_replace('\\', '/', $analyserName)), 0, -8) . 'OutputFormatter';

            if (class_exists($outputFormatter)) {
                $obj = new $outputFormatter();
                $obj($output, $analyserResults);
            }
        }

        if (isset($response['extra']['cache'])) {
            $stats = $response['extra']['cache'];
            $output->writeln(
                sprintf(
                    '%s<info>Cache: %d hits, %d misses</info>',
                    PHP_EOL,
                    $stats['hits'],
                    $stats['misses']
                )
            );
        }
    }
}
