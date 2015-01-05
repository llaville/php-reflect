<?php

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Analyser results
 *
 */
class Analyser extends OutputFormatter
{
    /**
     * Results of analysers available
     *
     * @param OutputInterface $output
     * @param array           $response
     */
    public function dir(OutputInterface $output, $response)
    {
        if (empty($response)) {
            $output->writeln('<error>No analysers detected.</error>');

        } else {
            $headers = array('Analyser Name', 'Analyser Class');
            $rows    = array();

            foreach ($response as $name => $class) {
                $rows[] = array($name, $class);
            }

            $this->tableHelper($output, $headers, $rows);
        }
    }

    /**
     * Results of analysers metrics
     *
     * @param OutputInterface $output
     * @param array           $response
     */
    public function run(OutputInterface $output, $response)
    {
        if (empty($response)) {
            // No reports printed if there are no metrics.
            $output->writeln('<info>No metrics.</info>');
            return;
        }

        $output->writeln('<info>Data Source Analysed</info>');

        $directories = array();

        foreach ($response['files'] as $file) {
            $directories[] = dirname($file);
        }
        $directories = array_unique($directories);

        // print Data Source summaries
        if (count($response['files']) > 0) {
            $text = sprintf(
                "%s" .
                "Directories                                 %10d%s" .
                "Files                                       %10d",
                PHP_EOL,
                count($directories),
                PHP_EOL,
                count($response['files'])
            );
            $output->writeln($text);
        }

        // print each analyser results
        foreach ($response as $analyserName => $analyserResults) {
            if (substr($analyserName, -8) !== 'Analyser') {
                continue;
            }
            $outputFormatter = 'Bartlett\Reflect\Console\Formatter\\' .
                substr($analyserName, 0, -8) . 'OutputFormatter';

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
