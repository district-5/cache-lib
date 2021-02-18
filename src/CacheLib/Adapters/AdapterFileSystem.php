<?php /** @noinspection SpellCheckingInspection */

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

use District5\CacheLib\Helper\FileSystemHelper;
use Exception;

/**
 * Class AdapterFileSystem
 *
 * Provides a file system based caching solution.
 *
 * @package District5\CacheLib\Adapters
 */
class AdapterFileSystem extends AdapterAbstract
{
    /**
     * Instance of FileSystemHelper
     *
     * @var FileSystemHelper|null
     */
    private $helper = null;

    /**
     * The path to the writable folder
     *
     * @var string|null
     */
    private $path = null;

    /**
     * Construct requires an array with the key of 'path', which should point
     * to a writable folder.
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        if (!array_key_exists('path', $config) || !is_dir($config['path']) || !is_writable($config['path'])) {
            throw new Exception('"path" key must be specified and be a valid location');
        }
        if (array_key_exists('prefix', $config)) {
            $this->prefix = $config['prefix'];
        } else {
            $this->prefix = 'OHAFS_';
        }
        $this->path = rtrim($config['path'], '/\\');
        $this->helper = new FileSystemHelper();
    }

    /**
     * Set a value in cache if it doesn't already exist. Internally, this uses the self::has() and
     * self::set() methods.
     *
     * @param string $key
     * @param bool|int|float|array|object|null $value
     * @param int $ttl
     * @return bool
     */
    public function setIfNotExists(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        if (false === $this->has($key)) {
            return $this->set($key, $value, $ttl);
        }

        return false;
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
        if (null === $pieces = $this->getPiecesFromKey($key)) {
            return $default;
        }

        $time = $pieces[0];
        unset($pieces[0]);
        $str = implode(PHP_EOL, $pieces);

        $val = @unserialize($str);
        if ($val === false || $time <= time()) {
            $this->remove($key);
            return $default;
        }

        return $val;
    }

    /**
     * @param string $key
     * @return array|null
     */
    protected function getPiecesFromKey(string $key): ?array
    {
        $md5 = md5($key);
        $folderPath = $this->getFolderPathFromMd5($md5);
        $path = $this->path . DIRECTORY_SEPARATOR . $folderPath . DIRECTORY_SEPARATOR . $this->getKeyString($md5);
        if (!file_exists($path) || !is_readable($path)) {
            return null;
        }

        $content = @file_get_contents($path);
        if (false === $content) {
            return null;
        }

        $pieces = explode(PHP_EOL, $content);
        if (!is_array($pieces) || !is_numeric($pieces[0])) {
            unlink($path);
            return null;
        }

        return $pieces;
    }

    /**
     * Get a folder path from a given MD5
     *
     * @param string $md5
     * @return string
     */
    private function getFolderPathFromMd5(string $md5): string
    {
        return sprintf(
            '%s%s%s',
            substr($md5, 0, 2),
            DIRECTORY_SEPARATOR,
            substr($md5, 2, 2)
        );
    }

    /**
     * Remove a value from cache by $key
     *
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool
    {
        $md5 = md5($key);
        $folderPath = $this->getFolderPathFromMd5($md5);
        $path = $this->path . DIRECTORY_SEPARATOR . $folderPath . DIRECTORY_SEPARATOR . $this->getKeyString($md5);
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }

        return unlink($path);
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
        $md5 = md5($key);
        $folderPath = $this->getFolderPathFromMd5($md5);
        $create = $this->helper->createPath($this->path, $folderPath);
        if (!$create) {
            return false;
        }
        $basePaths = $this->path . DIRECTORY_SEPARATOR . $folderPath;
        $path = $basePaths . DIRECTORY_SEPARATOR . $this->getKeyString($md5);

        $handle = fopen($path, 'w');
        if ($handle) {
            $data = time() + $ttl . PHP_EOL . serialize($value);
            $success = fwrite($handle, $data);
            @fclose($handle);
            return ($success !== false);
        }

        // @codeCoverageIgnoreStart
        return false;
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
        if (null === $pieces = $this->getPiecesFromKey($key)) {
            return false;
        }

        unset($pieces[0]);
        $str = implode(PHP_EOL, $pieces);
        $val = @unserialize($str);
        if ($val === false) {
            $this->remove($key);
            return false;
        }

        return $this->set($key, $val, $ttl);
    }

    /**
     * Flush the entire cache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        try {
            $this->helper->recursivelyDeleteFromDirectory($this->path, true);
            return true;

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }

        return false;
        // @codeCoverageIgnoreEnd
    }
}
