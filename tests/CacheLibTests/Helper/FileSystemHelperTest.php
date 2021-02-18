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

namespace District5Tests\CacheLib\Tests\Helper;

use District5\CacheLib\Helper\FileSystemHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class FileSystemHelperTest
 * @package District5\CacheLib\Tests\Helper
 */
class FileSystemHelperTest extends TestCase
{
    public function testMakeInvalids()
    {
        $fullPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'CacheLibTests';
        @mkdir($fullPath);
        $helper = new FileSystemHelper();
        $makeFake = $helper->createPath(
            DIRECTORY_SEPARATOR . 'no' . DIRECTORY_SEPARATOR . 'such' . DIRECTORY_SEPARATOR . 'path',
            'ok'
        );
        $this->assertFalse($makeFake);
        $makeEmptyPath = $helper->createPath($fullPath, '');
        $this->assertFalse($makeEmptyPath);
        // cover continue for empty paths.
        $directoryCreate = $helper->createPath($fullPath, 'tests' . DIRECTORY_SEPARATOR . 'directory');
        $this->assertTrue($directoryCreate);

        $helper->recursivelyDeleteFromDirectory($fullPath);
    }

    public function testRecursiveDelete()
    {
        $fullPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'CacheLibTests';
        mkdir($fullPath);
        touch($fullPath . DIRECTORY_SEPARATOR . '1');
        $this->assertFileExists($fullPath . DIRECTORY_SEPARATOR . '1');
        @mkdir($fullPath . DIRECTORY_SEPARATOR . 'dir');
        touch($fullPath . DIRECTORY_SEPARATOR . 'dir' . DIRECTORY_SEPARATOR . '2');
        $this->assertFileExists($fullPath . DIRECTORY_SEPARATOR . 'dir' . DIRECTORY_SEPARATOR . '2');
        @mkdir($fullPath . DIRECTORY_SEPARATOR . 'dir');
        $dir = new FileSystemHelper();
        $dir->recursivelyDeleteFromDirectory($fullPath . DIRECTORY_SEPARATOR . '1');
        $dir->recursivelyDeleteFromDirectory($fullPath);
        $this->assertFileNotExists($fullPath . DIRECTORY_SEPARATOR . '1');
        $this->assertFileNotExists($fullPath . DIRECTORY_SEPARATOR . 'dir' . DIRECTORY_SEPARATOR . '2');
    }
}
