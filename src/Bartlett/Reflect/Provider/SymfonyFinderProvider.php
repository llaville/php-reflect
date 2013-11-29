<?php

namespace Bartlett\Reflect\Provider;

class SymfonyFinderProvider
    implements ProviderInterface
{
    protected $provider;

    /**
     * Construct a new Symfony Finder data source provider.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     *
     * @return object
     */
    public function __construct(\Symfony\Component\Finder\Finder $finder)
    {
        $this->provider = $finder;
    }

    /**
     * Returns results of the data source provider.
     *
     * @return array
     * @throws \OutOfRangeException if $uri is illegal (unknown in this provider)
     */
    public function __invoke($uri = false)
    {
        $results = iterator_to_array($this->provider->getIterator());

        if (!$uri) {
            return $results;
        }
        if (isset($results[$uri])) {
            return array($uri => $results[$uri]);
        }
        throw new \OutOfRangeException("$uri does not exist in this provider.");
    }

    /**
     * Returns an Iterator for the current Symfony Finder configuration.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->provider->getIterator();
    }

    /**
     * Gets the count of item in the data source.
     *
     * @return int
     */
    public function count()
    {
        return $this->provider->count();
    }

}
