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
use Memcache;

/**
 * Class AdapterMemcache
 *
 * Interact with Memcache
 *
 * @package District5\CacheLib\Adapters
 */
class AdapterMemcache extends AdapterAbstract
{
    /**
     * @var Memcache|null
     */
    private ?Memcache $memcache = null;

    /**
     * Construct the adapter, giving an array of servers.
     * @param array $config
     * @example
     *     [
     *         'prefix' => '',
     *         'servers' => [
     *             [
     *                 'host' => 'cache1.example.com',
     *                 'port' => 11211,
     *                 'weight' => 1,
     *                 'timeout' => 60
     *             ],
     *             [
     *                 'host' => 'cache2.example.com',
     *                 'port' => 11211,
     *                 'weight' => 2,
     *                 'timeout' => 60
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
            $this->memcache = new Memcache();
            foreach ($config['servers'] as $server) {
                $this->memcache->addserver(
                    $server['host'],
                    $server['port'],
                    null,
                    $server['weight'],
                    $server['timeout']
                );
            }
        } catch (Exception $e) {
            // @codeCoverageIgnoreStart
            $this->memcache = null;
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
            return $this->memcache->set(
                $this->getKeyString($key),
                $value,
                $this->getFlagFromValue($value),
                $ttl
            );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Check if instance of \Memcache has been assigned
     * @return bool
     */
    private function hasConnection(): bool
    {
        return ($this->memcache instanceof Memcache);
    }

    /**
     * Establish the best flag to use for a given value
     *
     * @param mixed $value
     * @return int|null
     */
    private function getFlagFromValue($value): ?int
    {
        $flag = null;
        if (!is_bool($value) && !is_int($value) && !is_float($value)) {
            $flag = MEMCACHE_COMPRESSED;
        }

        return $flag;
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
            return $this->memcache->add(
                $this->getKeyString($key),
                $value,
                $this->getFlagFromValue($value),
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
     * @param string $key
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
            return $this->memcache->get(
                $this->getKeyString($key)
            );
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
        if ($value) {
            try {
                return $this->memcache->replace(
                    $this->getKeyString($key),
                    $value,
                    $this->getFlagFromValue($value),
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
            return $this->memcache->delete($this->getKeyString($key));
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
            return $this->memcache->flush();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return Memcache|null
     */
    public function getRawAdapter(): mixed
    {
        return $this->memcache;
    }
}
