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

use District5\CacheLib\Exception\ApcExtensionNotLoaded;

/**
 * Class AdapterApc
 *
 * Interact with APC
 *
 * @package District5\CacheLib\Adapters
 */
class AdapterApc extends AdapterAbstract
{
    /**
     * @var bool|null
     */
    private ?bool $apc = null;

    /**
     * Construct, and check the presence of the APC extension
     *
     * @param array $config
     * @throws ApcExtensionNotLoaded
     */
    public function __construct(array $config = [])
    {
        $this->checkExtension();
        if (array_key_exists('prefix', $config)) {
            $this->prefix = $config['prefix'];
        }
    }

    /**
     * Check if apc is enabled
     * @throws ApcExtensionNotLoaded
     */
    protected function checkExtension(): void
    {
        $this->apc = null;
        if (false !== extension_loaded('apc')) {
            $this->apc = true;
        }
        if (false !== extension_loaded('apcu')) {
            $this->apc = false;
        }
        if (null === $this->apc) {
            throw new ApcExtensionNotLoaded('Apc/Apcu Extension Not Loaded');
        }
    }

    /**
     * Get a value from cache by key name
     *
     * @param string $key
     * @param null|mixed $default
     * @return bool|int|float|array|object|null
     */
    public function get(string $key, $default = null): mixed
    {
        if ($this->apc === true) {
            $record = apc_fetch(
                $this->getKeyString($key),
                $found
            );
        } else {
            $record = apcu_fetch(
                $this->getKeyString($key),
                $found
            );
        }
        if ($found) {
            return $record;
        }

        return $default;
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
        if ($this->apc) {
            return apc_store(
                $this->getKeyString($key),
                $value,
                $ttl
            );
        }
        return apcu_store(
            $this->getKeyString($key),
            $value,
            $ttl
        );
    }

    /**
     * Set a value in cache if it doesn't already exist. Internally, this uses
     * apc_add
     *
     * @param string $key
     * @param bool|int|float|array|object|null $value
     * @param int $ttl
     * @return bool
     */
    public function setIfNotExists(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        if ($this->apc) {
            return apc_add(
                $this->getKeyString($key),
                $value,
                $ttl
            );
        }
        return apcu_add(
            $this->getKeyString($key),
            $value,
            $ttl
        );
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
        $key = $this->getKeyString($key);
        if ($this->apc) {
            return apc_exists($key);
        }
        return apcu_exists($key);
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
        $key = $this->getKeyString($key);
        if ($this->apc) {
            $val = apc_fetch($key, $fetched);
            if ($fetched) {
                return apc_store($key, $val, $ttl);
            }
        } else {
            $val = apcu_fetch($key, $fetched);
            if ($fetched) {
                return apcu_store($key, $val, $ttl);
            }
        }

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
        if ($this->apc) {
            return apc_delete($this->getKeyString($key));
        }
        return apcu_delete($this->getKeyString($key));
    }

    /**
     * Flush the entire cache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        if ($this->apc) {
            return apc_clear_cache();
        }
        return apcu_clear_cache();
    }

    /**
     * @return null
     */
    public function getRawAdapter(): mixed
    {
        return null;
    }
}
