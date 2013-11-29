<?php

namespace Bartlett\Reflect;

use Bartlett\Reflect\Provider\ProviderInterface;

class ProviderManager implements \Countable
{
    /**
     * @var array of ProviderInterface instances
     */
    protected $providers;

    /**
     * Create instance of the new provider manager
     *
     * @return object
     */
    public function __construct()
    {
        $this->providers = array();
    }

    /**
     * Tells how many providers are already registered.
     *
     * @return int
     */
    public function count()
    {
        return count($this->providers);
    }

    /**
     * Returns all providers registered at once.
     *
     * @return array
     */
    public function all()
    {
        return $this->providers;
    }

    /**
     * Checks if the current provider manager has a certain data source provider.
     *
     * @param string $alias A provider name
     *
     * @return bool TRUE if the provider has been registered, FALSE otherwise
     */
    public function has($alias)
    {
        return isset($this->providers[$alias]);
    }

    /**
     * Gets a registered provider by name.
     *
     * @param string $alias The provider name
     *
     * @return ProviderInterface The data source provider
     *
     * @throws \OutOfRangeException If there is no provider by that name
     */
    public function get($alias)
    {
        if (!$this->has($alias)) {
            throw new \OutOfRangeException(
                sprintf('There is no "%s" provider registered.', $alias)
            );
        }
        return $this->providers[$alias];
    }

    /**
     * Registers a provider to the current provider manager.
     *
     * @param string            $alias    The provider name
     * @param ProviderInterface $provider The data source provider
     *
     * @throws \InvalidArgumentException If the provider name is invalid
     */
    public function set($alias, ProviderInterface $provider)
    {
        if (!preg_match('/[A-Za-z0-9\._]/', $alias)) {
            throw new \InvalidArgumentException(
                sprintf('The provider name "%s" is invalid.', $alias)
            );
        }
        $this->providers[$alias] = $provider;
    }

    /**
     * Removes a registered provider.
     *
     * @return void
     * @throws \OutOfRangeException If there is no provider by that name
     */
    public function remove($alias)
    {
        if (!$this->has($alias)) {
            throw new \OutOfRangeException(
                sprintf('There is no "%s" provider registered.', $alias)
            );
        }
        unset($this->providers[$alias]);
    }

    /**
     * Clears all providers.
     *
     * @return void
     */
    public function clear()
    {
        $this->providers = array();
    }

}
