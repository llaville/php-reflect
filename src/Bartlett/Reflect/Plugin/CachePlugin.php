<?php declare(strict_types=1);

/**
 * Cache Plugin.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Plugin;

use Bartlett\Reflect\Plugin\Cache\CacheStorageInterface;
use Bartlett\Reflect\Plugin\Cache\DefaultCacheStorage;
use Bartlett\Reflect\Event\ProgressEvent;
use Bartlett\Reflect\Event\SuccessEvent;
use Bartlett\Reflect\Event\CompleteEvent;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use InvalidArgumentException;
use ReflectionException;
use ReflectionClass;

/**
 * Plugin that allow to cache parsing results
 * on different backend with any adapter.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha1
 */
class CachePlugin implements PluginInterface, EventSubscriberInterface
{
    const STATS_HITS    = 'hits';
    const STATS_MISSES  = 'misses';

    protected $storage;
    protected $stats;

    private $hashUserData;

    /**
     * Initializes the cache plugin.
     *
     * @param mixed $cache
     */
    public function __construct($cache)
    {
        if (is_array($cache)) {
            $cache = self::createCacheStorage($cache);
        }

        if (!$cache instanceof CacheStorageInterface) {
            throw new InvalidArgumentException(
                "Cannot initialize the cache plugin.\n" .
                'Expects an array or instance of CacheStorageInterface, and got ' .
                gettype($cache)
            );
        }

        $this->storage = $cache;
        $this->stats   = array(
            self::STATS_HITS   => 0,
            self::STATS_MISSES => 0,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function activate(EventDispatcherInterface $eventDispatcher): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            ProgressEvent::class => 'onReflectProgress',
            SuccessEvent::class  => 'onReflectSuccess',
            CompleteEvent::class => 'onReflectComplete',
        );
    }

    /**
     * Checks if results in cache will satisfy the source before parsing.
     *
     * @param ProgressEvent $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectProgress(ProgressEvent $event): void
    {
        if ($response = $this->storage->fetch($event->getArguments())) {
            ++$this->stats[self::STATS_HITS];
            $this->hashUserData = sha1(serialize($response));
            $event['notModified'] = $response;
        } else {
            $this->hashUserData = null;
        }
    }

    /**
     * If possible, store results in cache after source parsing.
     *
     * @param SuccessEvent $event Current event emitted by the manager (Reflect class)
     *
     * @return void
     */
    public function onReflectSuccess(SuccessEvent $event): void
    {
        if (sha1(serialize($event['ast'])) !== $this->hashUserData) {
            // cache need to be refresh
            ++$this->stats[self::STATS_MISSES];
            $this->storage->cache($event->getArguments());
        }
    }

    /**
     * Cache statistics at end of a parse process.
     *
     * @param CompleteEvent $event
     *
     * @return void
     */
    public function onReflectComplete(CompleteEvent $event): void
    {
        $event['extra'] = array('cache' => $this->stats);
    }

    /**
     * Get the current cache storage instance.
     *
     * @return CacheStorageInterface
     */
    public function getCacheStorage(): CacheStorageInterface
    {
        return $this->storage;
    }

    /**
     * Creates a default cache storage corresponding to $options
     *
     * @param array $options Cache configuration
     *
     * @return CacheStorageInterface
     * @throws InvalidArgumentException|ReflectionException
     */
    private static function createCacheStorage(array $options): CacheStorageInterface
    {
        $options = array_merge(
            array(
                'adapter' => 'DoctrineCacheAdapter',
                'backend' => array(
                    'class' => 'Doctrine\Common\Cache\FilesystemCache',
                    'args'  => array('%{TEMP}/bartlett/cache'),
                ),
            ),
            $options
        );

        if (!isset($options['adapter'])) {
            throw new InvalidArgumentException('Adapter is missing');
        }

        $adapterClass = $options['adapter'];

        if (strpos($adapterClass, '\\') === false) {
            // add default namespace
            $adapterClass = __NAMESPACE__ . '\Cache\\' . $adapterClass;
        }
        if (!class_exists($adapterClass)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Adapter "%s" cannot be loaded.',
                    $adapterClass
                )
            );
        }

        if (!isset($options['backend']['class'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Backend is missing for %s',
                    $adapterClass
                )
            );
        }
        $backendClass = $options['backend']['class'];

        if (!class_exists($backendClass)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Backend "%s" cannot be loaded.',
                    $backendClass
                )
            );
        }
        $rc = new ReflectionClass($backendClass);

        if (isset($options['backend']['args'])) {
            $args = self::replaceTokens($options['backend']['args']);
        } else {
            $args = array();
        }

        $backend      = $rc->newInstanceArgs($args);
        $cacheAdapter = new $adapterClass($backend);

        return new DefaultCacheStorage($cacheAdapter);
    }

    protected static function replaceTokens(array $args): array
    {
        for ($a = 0, $max = count($args); $a < $max; $a++) {
            if (!is_string($args[$a])) {
                continue;
            }
            // Expands variable from Environment on each argument
            $count = preg_match_all("/%{([^}]*)}/", $args[$a], $reg);
            for ($i = 0; $i < $count; $i++) {
                if ($reg[1][$i] == 'HOME') {
                    $reg[1][$i] = defined('PHP_WINDOWS_VERSION_BUILD')
                        ? 'USERPROFILE' : 'HOME';
                }
                $val = getenv($reg[1][$i]);
                if ($val) {
                    $args[$a] = str_replace(
                        $reg[0][$i],
                        $val,
                        $args[$a]
                    );
                } else {
                    // if TEMP env var is not defined, fallback to https://www.php.net/manual/en/function.sys-get-temp-dir.php
                    if ($reg[1][$i] == 'TEMP') {
                        $args[$a] = str_replace(
                            $reg[0][$i],
                            sys_get_temp_dir(),
                            $args[$a]
                        );
                    }
                }
            }
        }
        return $args;
    }
}
