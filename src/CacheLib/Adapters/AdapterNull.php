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
 * Class AdapterNull
 *
 * An adapter for use in Tests, or development environments
 *
 * @package District5\CacheLib\Adapters
 */
class AdapterNull extends AdapterAbstract
{
    /**
     * Empty constructor
     *
     * @param array $config (optional)
     */
    public function __construct(array $config = [])
    {
    }

    /**
     * Emulate the get of a cached value
     *
     * @param mixed $key
     * @param null|mixed $default
     * @return bool|int|float|array|object|null false
     */
    public function get(string $key, $default = null)
    {
        return false;
    }

    /**
     * Emulate the set of a cached key, value pair.
     *
     * @param string $key
     * @param bool|int|float|array|object|null $value
     * @param int $ttl (optional) default 86400
     * @return bool false
     */
    public function set(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        return false;
    }

    /**
     * Emulate the set of a cached key, value pair if it doesn't already exist.
     *
     * @param string $key
     * @param bool|int|float|array|object|null $value
     * @param int $ttl (optional) default 86400
     * @return bool false
     */
    public function setIfNotExists(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        return false;
    }

    /**
     * Emulate the has of a cached value
     *
     * @param string $key
     * @return bool false
     */
    public function has(string $key): bool
    {
        return false;
    }

    /**
     * Emulate the renew of a cached value
     *
     * @param string $key
     * @param int $ttl (optional) default 86400
     * @return bool false
     */
    public function renew(string $key, int $ttl = self::DEFAULT_TTL): bool
    {
        return false;
    }

    /**
     * Emulate the remove of a cached value
     *
     * @param string $key
     * @return bool false
     */
    public function remove(string $key): bool
    {
        return false;
    }

    /**
     * Emulate the flush of the cache
     *
     * @return bool false
     */
    public function flush(): bool
    {
        return false;
    }

    /**
     * @return null
     */
    public function getRawAdapter(): mixed
    {
        return null;
    }
}
