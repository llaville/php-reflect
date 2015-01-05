<?php

namespace Bartlett\Reflect\Console\Formatter;

use Symfony\Component\Console\Output\OutputInterface;

class StructureOutputFormatter extends OutputFormatter
{
    public function __invoke(OutputInterface $output, $count)
    {
        $count['constants'] = $count['classConstants']
            + $count['globalConstants']
            + $count['magicConstants'];

        $lines = array();

        $lines['namespaces'] = array(
            '  Namespaces                                %10d',
            array($count['namespaces'])
        );
        $lines['interfaces'] = array(
            '  Interfaces                                %10d',
            array($count['interfaces'])
        );
        $lines['traits'] = array(
            '  Traits                                    %10d',
            array($count['traits'])
        );

        $lines['classes'] = array(
            '  Classes                                   %10d',
            array($count['classes'])
        );
        $lines['abstractClasses'] = array(
            '    Abstract Classes                        %10d (%.2f%%)',
            array(
                $count['abstractClasses'],
                $count['classes'] > 0 ? ($count['abstractClasses'] / $count['classes']) * 100 : 0,
            )
        );
        $lines['concreteClasses'] = array(
            '    Concrete Classes                        %10d (%.2f%%)',
            array(
                $count['concreteClasses'],
                $count['classes'] > 0 ? ($count['concreteClasses'] / $count['classes']) * 100 : 0,
            )
        );

        $lines['methods'] = array(
            '  Methods                                   %10d',
            array($count['methods'])
        );
        $lines['methodsScope'] = array(
            '    Scope',
            array()
        );
        $lines['nonStaticMethods'] = array(
            '      Non-Static Methods                    %10d (%.2f%%)',
            array(
                $count['nonStaticMethods'],
                $count['methods'] > 0 ? ($count['nonStaticMethods'] / $count['methods']) * 100 : 0,
            )
        );
        $lines['staticMethods'] = array(
            '      Static Methods                        %10d (%.2f%%)',
            array(
                $count['staticMethods'],
                $count['methods'] > 0 ? ($count['staticMethods'] / $count['methods']) * 100 : 0,
            )
        );
        $lines['methodsVisibility'] = array(
            '    Visibility',
            array()
        );
        $lines['publicMethods'] = array(
            '      Public Method                         %10d (%.2f%%)',
            array(
                $count['publicMethods'],
                $count['methods'] > 0 ? ($count['publicMethods'] / $count['methods']) * 100 : 0,
            )
        );
        $lines['protectedMethods'] = array(
            '      Protected Method                      %10d (%.2f%%)',
            array(
                $count['protectedMethods'],
                $count['methods'] > 0 ? ($count['protectedMethods'] / $count['methods']) * 100 : 0,
            )
        );
        $lines['privateMethods'] = array(
            '      Private Method                        %10d (%.2f%%)',
            array(
                $count['privateMethods'],
                $count['methods'] > 0 ? ($count['privateMethods'] / $count['methods']) * 100 : 0,
            )
        );

        $lines['functions'] = array(
            '  Functions                                 %10d',
            array($count['functions'])
        );
        $lines['namedFunctions'] = array(
            '    Named Functions                         %10d (%.2f%%)',
            array(
                $count['namedFunctions'],
                $count['functions'] > 0 ? ($count['namedFunctions'] / $count['functions']) * 100 : 0,
            )
        );
        $lines['anonymousFunctions'] = array(
            '    Anonymous Functions                     %10d (%.2f%%)',
            array(
                $count['anonymousFunctions'],
                $count['functions'] > 0 ? ($count['anonymousFunctions'] / $count['functions']) * 100 : 0,
            )
        );

        $lines['constants'] = array(
            '  Constants                                 %10d',
            array($count['constants'])
        );
        $lines['globalConstants'] = array(
            '    Global Constants                        %10d (%.2f%%)',
            array(
                $count['globalConstants'],
                $count['constants'] > 0 ? ($count['globalConstants'] / $count['constants']) * 100 : 0,
            )
        );
        $lines['magicConstants'] = array(
            '    Magic Constants                         %10d (%.2f%%)',
            array(
                $count['magicConstants'],
                $count['constants'] > 0 ? ($count['magicConstants'] / $count['constants']) * 100 : 0,
            )
        );
        $lines['classConstants'] = array(
            '    Class Constants                         %10d (%.2f%%)',
            array(
                $count['classConstants'],
                $count['constants'] > 0 ? ($count['classConstants'] / $count['constants']) * 100 : 0,
            )
        );

        $lines['tests'] = array(
            '  Tests',
            array()
        );
        $lines['testClasses'] = array(
            '    Classes                                 %10d',
            array(
                $count['testClasses'],
            )
        );
        $lines['testMethods'] = array(
            '    Methods                                 %10d',
            array(
                $count['testMethods'],
            )
        );

        $output->writeln(sprintf('%s<info>Structure</info>', PHP_EOL));
        $this->printFormattedLines($output, $lines);
    }
}
