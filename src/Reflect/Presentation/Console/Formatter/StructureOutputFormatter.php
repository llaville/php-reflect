<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console\Formatter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Structure Analyser formatter class for console output.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class StructureOutputFormatter extends OutputFormatter
{
    /**
     * Structure Analyser console output format
     *
     * @param OutputInterface $output Console Output concrete instance
     * @param array           $count  Structure Analyser metrics
     *
     * @return void
     */
    public function __invoke(OutputInterface $output, array $count): void
    {
        if (is_int($count['classConstants'])
            && is_int($count['globalConstants'])
            && is_int($count['magicConstants'])
        ) {
            $count['constants'] = $count['classConstants']
                + $count['globalConstants']
                + $count['magicConstants'];
        }

        $lines = [];

        if (array_key_exists('namespaces', $count)
            && is_int($count['namespaces'])
        ) {
            $lines['namespaces'] = array(
                '  Namespaces                                %10d',
                array($count['namespaces'])
            );
        }

        if (array_key_exists('interfaces', $count)
            && is_int($count['interfaces'])
        ) {
            $lines['interfaces'] = array(
                '  Interfaces                                %10d',
                array($count['interfaces'])
            );
        }

        if (array_key_exists('traits', $count)
            && is_int($count['traits'])
        ) {
            $lines['traits'] = array(
                '  Traits                                    %10d',
                array($count['traits'])
            );
        }

        if (array_key_exists('classes', $count)
            && is_int($count['classes'])
        ) {
            $lines['classes'] = array(
                '  Classes                                   %10d',
                array($count['classes'])
            );

            if (array_key_exists('abstractClasses', $count)
                && is_int($count['abstractClasses'])
            ) {
                $lines['abstractClasses'] = array(
                    '    Abstract Classes                        %10d (%.2f%%)',
                    array(
                        $count['abstractClasses'],
                        $count['classes'] > 0 ? ($count['abstractClasses'] / $count['classes']) * 100 : 0,
                    )
                );
            }
            if (array_key_exists('concreteClasses', $count)
                && is_int($count['concreteClasses'])
            ) {
                $lines['concreteClasses'] = array(
                    '    Concrete Classes                        %10d (%.2f%%)',
                    array(
                        $count['concreteClasses'],
                        $count['classes'] > 0 ? ($count['concreteClasses'] / $count['classes']) * 100 : 0,
                    )
                );
            }
        }

        if (array_key_exists('methods', $count)
            && is_int($count['methods'])
        ) {
            $lines['methods'] = array(
                '  Methods                                   %10d',
                array($count['methods'])
            );

            $lines['methodsScope'] = array(
                '    Scope',
                []
            );
            if (array_key_exists('nonStaticMethods', $count)
                && is_int($count['nonStaticMethods'])
            ) {
                $lines['nonStaticMethods'] = array(
                    '      Non-Static Methods                    %10d (%.2f%%)',
                    array(
                        $count['nonStaticMethods'],
                        $count['methods'] > 0 ? ($count['nonStaticMethods'] / $count['methods']) * 100 : 0,
                    )
                );
            }
            if (array_key_exists('staticMethods', $count)
                && is_int($count['staticMethods'])
            ) {
                $lines['staticMethods'] = array(
                    '      Static Methods                        %10d (%.2f%%)',
                    array(
                        $count['staticMethods'],
                        $count['methods'] > 0 ? ($count['staticMethods'] / $count['methods']) * 100 : 0,
                    )
                );
            }

            $lines['methodsVisibility'] = array(
                '    Visibility',
                []
            );
            if (array_key_exists('publicMethods', $count)
                && is_int($count['publicMethods'])
            ) {
                $lines['publicMethods'] = array(
                    '      Public Method                         %10d (%.2f%%)',
                    array(
                        $count['publicMethods'],
                        $count['methods'] > 0 ? ($count['publicMethods'] / $count['methods']) * 100 : 0,
                    )
                );
            }
            if (array_key_exists('protectedMethods', $count)
                && is_int($count['protectedMethods'])
            ) {
                $lines['protectedMethods'] = array(
                    '      Protected Method                      %10d (%.2f%%)',
                    array(
                        $count['protectedMethods'],
                        $count['methods'] > 0 ? ($count['protectedMethods'] / $count['methods']) * 100 : 0,
                    )
                );
            }
            if (array_key_exists('privateMethods', $count)
                && is_int($count['privateMethods'])
            ) {
                $lines['privateMethods'] = array(
                    '      Private Method                        %10d (%.2f%%)',
                    array(
                        $count['privateMethods'],
                        $count['methods'] > 0 ? ($count['privateMethods'] / $count['methods']) * 100 : 0,
                    )
                );
            }
        }

        if (array_key_exists('functions', $count)
            && is_int($count['functions'])
        ) {
            $lines['functions'] = array(
                '  Functions                                 %10d',
                array($count['functions'])
            );
            if (array_key_exists('namedFunctions', $count)
                && is_int($count['namedFunctions'])
            ) {
                $lines['namedFunctions'] = array(
                    '    Named Functions                         %10d (%.2f%%)',
                    array(
                        $count['namedFunctions'],
                        $count['functions'] > 0 ? ($count['namedFunctions'] / $count['functions']) * 100 : 0,
                    )
                );
            }
            if (array_key_exists('anonymousFunctions', $count)
                && is_int($count['anonymousFunctions'])
            ) {
                $lines['anonymousFunctions'] = array(
                    '    Anonymous Functions                     %10d (%.2f%%)',
                    array(
                        $count['anonymousFunctions'],
                        $count['functions'] > 0 ? ($count['anonymousFunctions'] / $count['functions']) * 100 : 0,
                    )
                );
            }
        }

        if (array_key_exists('constants', $count)
            && is_int($count['constants'])
        ) {
            $lines['constants'] = array(
                '  Constants                                 %10d',
                array($count['constants'])
            );

            if (array_key_exists('globalConstants', $count)
                && is_int($count['globalConstants'])
            ) {
                $lines['globalConstants'] = array(
                    '    Global Constants                        %10d (%.2f%%)',
                    array(
                        $count['globalConstants'],
                        $count['constants'] > 0 ? ($count['globalConstants'] / $count['constants']) * 100 : 0,
                    )
                );
            }
            if (array_key_exists('magicConstants', $count)
                && is_int($count['magicConstants'])
            ) {
                $lines['magicConstants'] = array(
                    '    Magic Constants                         %10d (%.2f%%)',
                    array(
                        $count['magicConstants'],
                        $count['constants'] > 0 ? ($count['magicConstants'] / $count['constants']) * 100 : 0,
                    )
                );
            }
            if (array_key_exists('classConstants', $count)
                && is_int($count['classConstants'])
            ) {
                $lines['classConstants'] = array(
                    '    Class Constants                         %10d (%.2f%%)',
                    array(
                        $count['classConstants'],
                        $count['constants'] > 0 ? ($count['classConstants'] / $count['constants']) * 100 : 0,
                    )
                );
            }
        }

        if (array_key_exists('testClasses', $count)
            && is_int($count['testClasses'])
        ) {
            $lines['tests'] = array(
                '  Tests',
                []
            );

            $lines['testClasses'] = array(
                '    Classes                                 %10d',
                array(
                    $count['testClasses'],
                )
            );
            if (array_key_exists('testMethods', $count)
                && is_int($count['testMethods'])
            ) {
                $lines['testMethods'] = array(
                    '    Methods                                 %10d',
                    array(
                        $count['testMethods'],
                    )
                );
            }
        }

        $output->writeln(sprintf('%s<info>Structure</info>', PHP_EOL));
        $this->printFormattedLines($output, $lines);
    }
}
