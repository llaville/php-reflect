<?php

namespace Bartlett\Reflect\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Base class for all commands.
 *
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
