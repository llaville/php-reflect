<?php

namespace Bartlett\Reflect\Console\Formatter;

use Symfony\Component\Console\Output\OutputInterface;

class LocOutputFormatter extends OutputFormatter
{
    public function __invoke(OutputInterface $output, $count)
    {
        $count['ncloc'] = $count['loc'] - $count['cloc'];

        if ($count['classes'] > 0) {
            $count['llocByNoc'] = $count['llocClasses'] / $count['classes'];
        }

        if ($count['methods'] > 0) {
            $count['llocByNom'] = $count['llocClasses'] / $count['methods'];
        }

        if ($count['functions'] > 0) {
            $count['llocByNof'] = $count['llocFunctions'] / $count['functions'];
        }

        $count['llocGlobal'] = $count['lloc']
            - $count['llocClasses']
            - $count['llocFunctions']
        ;

        if ($count['lloc'] > 0) {
            $count['ccnByLloc'] = $count['ccn'] / $count['lloc'];
        }

        if ($count['methods'] > 0) {
            $count['ccnByNom'] = ($count['methods'] + $count['ccnMethods'])
                / $count['methods']
            ;
        }

        $lines = array();

        $lines['loc'] = array(
            '  Lines of Code (LOC)                       %10d',
            array($count['loc'])
        );
        $lines['cloc'] = array(
            '  Comment Lines of Code (CLOC)              %10d (%.2f%%)',
            array(
                $count['cloc'],
                $count['loc'] > 0 ? ($count['cloc'] / $count['loc']) * 100 : 0,
            )
        );
        $lines['ncloc'] = array(
            '  Non-Comment Lines of Code (NCLOC)         %10d (%.2f%%)',
            array(
                $count['ncloc'],
                $count['loc'] > 0 ? ($count['ncloc'] / $count['loc']) * 100 : 0,
            )
        );
        $lines['lloc'] = array(
            '  Logical Lines of Code (LLOC)              %10d (%.2f%%)',
            array(
                $count['lloc'],
                $count['loc'] > 0 ? ($count['lloc'] / $count['loc']) * 100 : 0,
            )
        );
        $lines['llocClasses'] = array(
            '    Classes                                 %10d (%.2f%%)',
            array(
                $count['llocClasses'],
                $count['lloc'] > 0 ? ($count['llocClasses'] / $count['lloc']) * 100 : 0,
            )
        );
        $lines['llocByNoc'] = array(
            '      Average Class Length                  %10d',
            array(
                $count['llocByNoc'],
            )
        );
        $lines['llocByNom'] = array(
            '      Average Method Length                 %10d',
            array(
                $count['llocByNom'],
            )
        );
        $lines['llocFunctions'] = array(
            '    Functions                               %10d (%.2f%%)',
            array(
                $count['llocFunctions'],
                $count['lloc'] > 0 ? ($count['llocFunctions'] / $count['lloc']) * 100 : 0,
            )
        );
        $lines['llocByNof'] = array(
            '      Average Function Length               %10d',
            array(
                $count['llocByNof'],
            )
        );
        $lines['llocGlobal'] = array(
            '    Not in classes or functions             %10d (%.2f%%)',
            array(
                $count['llocGlobal'],
                $count['lloc'] > 0 ? ($count['llocGlobal'] / $count['lloc']) * 100 : 0,
            )
        );

        $output->writeln(sprintf('%s<info>Size</info>', PHP_EOL));
        $this->printFormattedLines($output, $lines);

        $lines = array();

        $lines['ccnByLloc'] = array(
            '  Cyclomatic Complexity / LLOC              %10.2f',
            array($count['ccnByLloc'])
        );
        $lines['ccnByNom'] = array(
            '  Cyclomatic Complexity / Number of Methods %10.2f',
            array($count['ccnByNom'])
        );

        $output->writeln(sprintf('%s<info>Complexity</info>', PHP_EOL));
        $this->printFormattedLines($output, $lines);
    }
}
