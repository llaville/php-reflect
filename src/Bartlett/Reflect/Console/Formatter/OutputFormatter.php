<?php

namespace Bartlett\Reflect\Console\Formatter;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Formatter\OutputFormatter as BaseOutputFormatter;

class OutputFormatter extends BaseOutputFormatter
{
    const JSON_PRETTY_PRINT = 128;

    protected function tableHelper(OutputInterface $output, $headers, $rows, $style = 'compact')
    {
        $table = new Table($output);
        $table->setStyle($style)
            ->setHeaders($headers)
            ->setRows($rows)
            ->render()
        ;
    }

    protected function printFormattedLines(OutputInterface $output, array $lines)
    {
        foreach ($lines as $ident => $contents) {
            list ($format, $args) = $contents;
            $output->writeln(vsprintf($format, $args));
        }
    }

    /**
     * Transforms compatibility analyser results to standard json format.
     *
     * @param OutputInterface $output
     * @param mixed           $response
     *
     * @return void
     */
    public function transformToJson(OutputInterface $output, $response)
    {
        $output->write(
            json_encode($response, self::JSON_PRETTY_PRINT),
            OutputInterface::OUTPUT_RAW
        );
    }

    /**
     * Transforms compatibility analyser results to Composer json format.
     *
     * @param OutputInterface $output
     * @param mixed           $response
     *
     * @return void
     * @throws \RuntimeException
     */
    public function transformToComposer(OutputInterface $output, $response)
    {
        $analyserId = 'Bartlett\CompatInfo\Analyser\CompatibilityAnalyser';
        if (!isset($response[$analyserId])) {
            throw new \RuntimeException('Could not render result to Composer format');
        }
        $compatinfo = $response[$analyserId];

        // include PHP version
        $composer = array(
            'php' => '>= ' . $compatinfo['versions']['php.min']
        );

        // include extensions
        foreach ($compatinfo['extensions'] as $key => $val) {
            if (in_array($key, array('standard', 'Core'))) {
                continue;
            }
            $composer['ext-' . $key] = '*';
        }

        // final result
        $composer = array('require' => $composer);

        $this->transformToJson($output, $composer);
    }
}
