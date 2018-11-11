<?php
/**
 * Base class for all console commands
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Base class for all commands.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Command extends BaseCommand
{
    protected $enabled = true;

    /**
     * Disables the command in the current environment
     *
     * @return Command The current instance
     */
    public function disable()
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * Checks whether the command is enabled or not in the current environment
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
