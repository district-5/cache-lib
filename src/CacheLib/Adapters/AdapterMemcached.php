<?php /** @noinspection PhpComposerExtensionStubsInspection */

/**
 * District5 - CacheLib
 *
 * @copyright District5
 *
 * @author District5
 * @link https://www.district5.co.uk
 *
 * @license This software and associated documentation (the "Software") may not be
 * used, copied, modified, distributed, published or licensed to any 3rd party
 * without the written permission of District5 or its author.
 *
 * The above copyright notice and this permission notice shall be included in
 * all licensed copies of the Software.
 *
 */

namespace District5\CacheLib\Adapters;

use Exception;
use Memcached;

/**
 * Class AdapterMemcached
 *
 * Interact with Memcached
 *
 * @package District5\CacheLib\Adapters
 */
class AdapterMemcached extends AdapterAbstract
{
    /**
     * @var Memcached
     */
    private $memcached = null;

    /**
     * Construct the adapter, giving an array of servers.
     * @param array $config
     * @example
     *     [
     *         'prefix' => '',
     *         'persistent_id' => '',
     *         'servers' => [
     *             [
     *                 'host' => 'cache1.example.com',
     *                 'port' => 11211,
     *                 'weight' => 1
     *             ],
     *             [
     *                 'host' => 'cache2.example.com',
     *                 'port' => 11211,
     *                 'weight' => 2
     *             ]
     *         ]
     *     ]
     */
    public function __construct(array $config = [])
    {
        try {
            if (array_key_exists('prefix', $config)) {
                $this->prefix = $config['prefix'];
            }
            if (array_key_exists('persistent_id', $config) && !empty($config['persistent_id'])) {
                // @codeCoverageIgnoreStart
                $this->memcached = new Memcached($config['persistent_id']);
            } else {
                // @codeCoverageIgnoreEnd
                $this->memcached = new Memcached();
            }
            foreach ($config['servers'] as $server) {
                $this->memcached->addserver(
                    $server['host'],
                    $server['port'],
                    $server['weight']
                );
            }
            if (array_key_exists('options', $config)) {
                foreach ($config['options'] as $optionKey => $optionValue) {
                    $this->memcached->setOption($optionKey, $optionValue);
                }
            }
        } catch (Exception $e) {
            // @codeCoverageIgnoreStart
            $this->memcached = null;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Set a value in cache by key name, specifying the TTL
     *
     * @param string $key
     * @param bool|int|float|array|object|null $value
     * @param int $ttl
     * @return bool
     */
    public function set(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            return $this->memcached->set(
                $this->getKeyString($key),
                $value,
                $ttl
            );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check if instance of Memcached has been assigned
     *
     * @return bool
     */
    private function hasConnection(): bool
    {
        return ($this->memcached instanceof Memcached);
    }

    /**
     * Set a value in cache if it doesn't already exist. Internally, this uses
     * Memcache::add
     *
     * @param string $key
     * @param bool|int|float|array|object|null $value
     * @param int $ttl
     * @return bool
     */
    public function setIfNotExists(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            return $this->memcached->add(
                $this->getKeyString($key),
                $value,
                $ttl
            );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Establish whether the cache contains a value with key of $key. Internally,
     * this method performs a get() on the key, so it's worth using get() instead
     * if you require a value.
     * Don't use:
     *   if (has('x')) { $a = get('x'); }
     * Use:
     *   if (false != ($a = get('x')) { // ok. }
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return ($this->get($key) !== null);
    }

    /**
     * Get a value from cache by key name
     *
     * @param mixed $key
     * @param null|mixed $default
     * @return bool|int|float|array|object|null
     */
    public function get(string $key, $default = null)
    {
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return $default;
            // @codeCoverageIgnoreEnd
        }

        try {
            $result = $this->memcached->get(
                $this->getKeyString($key)
            );
            if ($result === Memcached::RES_NOTFOUND) {
                return $default;
            }

            return $result;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }

        return $default;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Alter the TTL for a given key. Essentially, renewing it in
     * the cache.
     *
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function renew(string $key, int $ttl = self::DEFAULT_TTL): bool
    {
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        $value = $this->get($key);
        if ($value !== false) {
            try {
                return $this->memcached->replace(
                    $this->getKeyString($key),
                    $value,
                    $ttl
                );
                // @codeCoverageIgnoreStart
            } catch (Exception $e) {
            }
        }
        // @codeCoverageIgnoreEnd

        return false;
    }

    /**
     * Remove a value from cache by $key
     *
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool
    {
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            /** @noinspection PhpRedundantOptionalArgumentInspection */
            return $this->memcached->delete($this->getKeyString($key), 0);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Flush the entire cache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        if (!$this->hasConnection()) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        try {
            return $this->memcached->flush();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }
}
