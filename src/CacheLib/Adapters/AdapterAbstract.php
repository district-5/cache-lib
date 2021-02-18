<?php
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

/**
 * Class AdapterAbstract
 *
 * Abstract class for CacheLib Adapters
 *
 * @package District5\CacheLib\Adapters
 */
abstract class AdapterAbstract
{
    /**
     * @var int
     */
    const DEFAULT_TTL = 86400;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * Constructor with optional config array
     *
     * @param array $config (optional)
     */
    abstract public function __construct(array $config = []);

    /**
     * Get a value from cache by key name
     *
     * @param string $key
     * @param null|mixed $default
     * @return bool|int|float|array|object|null
     */
    abstract public function get(string $key, $default = null);

    /**
     * Set a value in cache by key name, specifying the TTL
     *
     * @param string $key
     * @param bool|int|float|array|object|null $value
     * @param int $ttl
     * @return bool
     */
    abstract public function set(string $key, $value, int $ttl = self::DEFAULT_TTL): bool;

    /**
     * Set a value in cache if it doesn't already exist.
     *
     * @param string $key
     * @param bool|int|float|array|object|null $value
     * @param int $ttl
     * @return bool
     */
    abstract public function setIfNotExists(string $key, $value, int $ttl = self::DEFAULT_TTL): bool;

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
    abstract public function has(string $key): bool;

    /**
     * Alter the TTL for a given key. Essentially, renewing it in
     * the cache.
     *
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    abstract public function renew(string $key, int $ttl = self::DEFAULT_TTL): bool;

    /**
     * Remove a value from cache by $key
     *
     * @param string $key
     * @return bool
     */
    abstract public function remove(string $key): bool;

    /**
     * Flush the entire cache.
     *
     * @return bool
     */
    abstract public function flush(): bool;

    /**
     * Get a key name for a cache value.
     *
     * @param string $name
     * @return string
     */
    protected function getKeyString(string $name): string
    {
        return $this->prefix . $name;
    }
}
