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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to validate structure of the JSON configuration file.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.3.0
 */
class ValidateCommand extends ProviderCommand
{
    protected function configure()
    {
        $env  = $this->getApplication()->getEnv();
        $file = $env->getJsonFilename();

        $this
            ->setName('validate')
            ->setDescription('Validates a ' . $file)
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'Path to ' . $file . ' file',
                getenv($env->getEnv()) ? : './' . $file
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $var = parent::execute($input, $output);

        if (is_int($var)) {
            // json config file is missing or invalid
            return $var;
        }
        $output->writeln('<info>' . $input->getArgument('file') . ' is valid</info>');
    }
}
