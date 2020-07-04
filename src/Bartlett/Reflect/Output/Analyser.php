<?php declare(strict_types=1);

/**
 * Default console output class for Analyser Api.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Output;

use Bartlett\Reflect\Console\Formatter\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Analyser results default render on console
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha1
 */
class Analyser extends OutputFormatter
{
    /**
     * Results of analysers available
     *
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Analysers list
     *
     * @return void
     */
    public function dir(OutputInterface $output, $response): void
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
     * @param OutputInterface $output   Console Output concrete instance
     * @param array           $response Analyser metrics
     *
     * @return void
     */
    public function run(OutputInterface $output, $response): void
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
