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

namespace District5\CacheLib\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class FileSystemHelper
 *
 * Provides a simple way of interacting with the file system
 *
 * @package District5\CacheLib\Helper
 */
class FileSystemHelper
{
    /**
     * Create a new path on top of a base directory
     *
     * @param string $base
     * @param string $path
     * @return bool
     */
    public function createPath(string $base, string $path): bool
    {
        if (!is_dir($base) || !is_writable($base)) {
            return false;
        }

        if (empty($path)) {
            return false;
        }

        $pieces = explode(DIRECTORY_SEPARATOR, $path);
        $dir = $base;
        foreach ($pieces as $directory) {
            if (empty($directory)) {
                // @codeCoverageIgnoreStart
                continue; // Handle the / from the exploded path
                // @codeCoverageIgnoreEnd
            }

            $singlePath = $dir . DIRECTORY_SEPARATOR . $directory;
            if (!file_exists($singlePath) || !is_dir($singlePath)) {
                $create = @mkdir($singlePath);
                if ($create === false) {
                    // @codeCoverageIgnoreStart
                    return false;
                    // @codeCoverageIgnoreEnd
                }
            }
            $dir = $singlePath;
        }

        return true;
    }

    /**
     * Recursively delete files and folders, when given a base path
     *
     * @param string $baseDirectory
     * @param bool $start (optional) default false
     * @return void
     * @noinspection PhpRedundantOptionalArgumentInspection
     */
    public function recursivelyDeleteFromDirectory(string $baseDirectory, $start = false)
    {
        if (!is_dir($baseDirectory)) {
            return;
        }

        $iterator = new RecursiveDirectoryIterator($baseDirectory);
        $files = new RecursiveIteratorIterator(
            $iterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            /* @var $file SplFileInfo */
            $fileName = $file->getFilename();
            if (in_array($fileName, ['.', '..'])) {
                continue;
            }

            if ($file->isDir()) {
                $this->recursivelyDeleteFromDirectory($file->getRealPath(), false);
            } else {
                if ($start === false) {
                    unlink($file->getRealPath());
                }
            }
        }

        if ($start === false) {
            rmdir($baseDirectory);
        }
    }
}
