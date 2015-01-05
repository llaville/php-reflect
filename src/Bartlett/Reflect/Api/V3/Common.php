<?php

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect\Environment;
use Bartlett\Reflect\Api\V3\Config;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\Finder\Finder;

abstract class Common
{
    protected $dataSourceId;
    protected $provider;
    protected $eventDispatcher;

    /**
     * Set the EventDispatcher of the request
     *
     * @param EventDispatcherInterface $eventDispatcher Instance of the event
     *        dispatcher
     *
     * @return self for a fuent interface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     *
     * @param string $source Path to the data source (dir, file, archive)
     * @param string $alias  Shortcut that referenced the data source
     *
     * @return Finder|boolean
     */
    protected function findProvider($source, $alias)
    {
        if (!$alias) {
            $src = realpath($source);
            if (PATH_SEPARATOR == ';') {
                // on windows platform, remove the drive identifier
                $src = substr($src, 2);
            }

            if (is_dir($src)) {
                $provider = array('in' => $src);

            } elseif (is_file($src)) {
                $ext = pathinfo($src, PATHINFO_EXTENSION);

                if (in_array($ext, array('phar', 'zip', 'gz', 'tar', 'tgz', 'rar'))) {
                    // archive file
                    $provider = array('in' => 'phar://' . $src);
                } else {
                    $provider = array('in' => dirname($src), 'name' => basename($src));
                    $this->dataSourceId = $src;
                }
            }
        }

        if (!isset($provider)) {
            // when $source identify an entry of a json config file
            $filename = Environment::getJsonConfigFilename();

            if ($filename === false) {
                return false;
            }

            // try to validate syntax and content of this json config file
            $config = new Config();
            $var    = $config->validate($filename);

            foreach ($var['source-providers'] as $provider) {
                $in = trim($provider['in']);
                if (empty($in)) {
                    // this source provider is incomplete: empty "in" key
                    return false;
                }
                $src = explode(' as ', $in);
                $src = array_map('trim', $src);

                if (!empty($alias) && count($src) < 2) {
                    // searching on alias, which is not provided
                    continue;
                }
                $i = empty($alias) ? 0 : 1;
                // search on data source path ($i = 0) or alias ($i = 1)
                if ($src[$i] == $source) {
                    $provider['in'] = $src[0];
                    break;
                }
                unset($provider);
            }

            if (!isset($provider)) {
                // data source not found
                return false;
            }
        }

        if (substr($provider['in'], 0, 1) == '.') {
            // relative local file
            $provider['in'] = realpath($provider['in']);
        }
        if (PATH_SEPARATOR == ';') {
            // normalizes path to unix format
            $provider['in'] = str_replace(DIRECTORY_SEPARATOR, '/', $provider['in']);
        }
        if (!isset($provider['name'])) {
            // default file extensions to scan
            $provider['name'] = '/\\.(php|inc|phtml)$/';
        }
        if (!isset($this->dataSourceId)) {
            $this->dataSourceId = $provider['in'];
        }

        $finder = new Finder();
        $finder->files();

        $constraints = array(
            'in',                       // Location
            'exclude',                  // Exclude directories
            'name', 'notName',          // File name constraints
            'path', 'notPath',          // Path constraints
            'size',                     // File size constraints
            'date',                     // File date constraints
            'depth',                    // Directory depth constraints
            'contains', 'notContains',  // File contents constraints
        );
        foreach ($constraints as $constraint) {
            if (isset($provider[$constraint])) {
                if (is_array($provider[$constraint])) {
                    $args = $provider[$constraint];
                } else {
                    $args = array($provider[$constraint]);
                }
                foreach ($args as $arg) {
                    $finder->{$constraint}($arg);
                }
            }
        }
        $this->provider = $provider;
        return $finder;
    }
}
