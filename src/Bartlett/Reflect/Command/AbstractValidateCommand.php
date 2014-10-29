<?php
/**
 * Validate console command.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

/**
 * Console command to validate structure of the JSON configuration file.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.6.0
 */
abstract class AbstractValidateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('validate')
            ->setDescription('Validates a ' . $this->jsonFile)
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'Path to ' . $this->jsonFile . ' file',
                getenv($this->env) ? : './' . $this->jsonFile
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file  = $input->getArgument('file');
        $fname = realpath($file);

        if (!file_exists($fname)) {
            $output->writeln('<error>' . $file . ' not found.</error>');
            return 1;
        }
        if (!is_readable($fname)) {
            $output->writeln('<error>' . $file . ' is not readable.</error>');
            return 1;
        }

        $json = file_get_contents($file);

        try {
            $parser = new JsonParser();
            $parser->parse($json);
        } catch (ParsingException $e) {
            $fmt = $this->getApplication()->getHelperSet()->get('formatter');

            $output->writeln(
                $fmt->formatBlock(
                    explode("\n", $e->getMessage()),
                    'error'
                )
            );
            return 1;
        }
        $output->writeln('<info>' . $file . ' is valid</info>');
    }
}
