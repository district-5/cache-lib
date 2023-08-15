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

namespace District5Tests\CacheLibTests\Adapters;

use District5\CacheLib\Adapters\AdapterFileSystem;
use District5\CacheLib\Helper\FileSystemHelper;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class AdapterFileSystemTest
 * @package District5Tests\CacheLibTests\Adapters
 */
class AdapterFileSystemTest extends TestCase
{
    /**
     * @var AdapterFileSystem|null
     */
    private ?AdapterFileSystem $adapter = null;

    /**
     * @var FileSystemHelper|null
     */
    private ?FileSystemHelper $dir = null;

    /**
     * @var string|null
     */
    private ?string $path = null;

    /**
     * @var string|null
     */
    private ?string $name = 'foo';

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->dir = new FileSystemHelper();
        $tmpDir = sys_get_temp_dir();
        $this->assertTrue($this->dir->createPath($tmpDir, 'CacheLibTests'));
        $this->path = $tmpDir . DIRECTORY_SEPARATOR . 'CacheLibTests';
        $this->adapter = new AdapterFileSystem(['path' => $this->path, 'prefix' => 'TEST_']);
    }

    public function testSetGet()
    {
        $this->assertTrue($this->adapter->set($this->name, 'bar', 10));
        $this->assertEquals('bar', $this->adapter->get($this->name));
    }

    public function testSetGetTtlExpired()
    {
        $this->assertTrue($this->adapter->set($this->name, 'slept', 1));
        sleep(2);
        $this->assertNull($this->adapter->get($this->name));
    }

    public function testSetIfNotExistsGet()
    {
        $str = md5(time());
        $this->assertTrue($this->adapter->setIfNotExists($str, 'foobar', 10));
        $this->assertFalse($this->adapter->setIfNotExists($str, 'barfoo', 10));
    }

    public function testAssertException()
    {
        $this->expectException(Exception::class);
        new AdapterFileSystem([]);
    }

    public function testGetInvalid()
    {
        $name = rand(0, 999);
        $md5 = md5($name);
        $file = $this->path . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 0, 2) . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 2, 2) . DIRECTORY_SEPARATOR;
        $file .= 'OHAFS_' . $md5;
        $this->dir->createPath(
            $this->path,
            substr($md5, 0, 2) . DIRECTORY_SEPARATOR . substr($md5, 2, 2)
        );
        $handle = fopen($file, 'w');
        fwrite($handle, (time() + 100) . PHP_EOL . 'a;x/sdr');
        fclose($handle);
        chmod($file, 701);
        $adapter = clone $this->adapter;
        $this->assertNull($adapter->get($name));

        $name = rand(0, 999);
        $md5 = md5($name);
        $file = $this->path . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 0, 2) . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 2, 2) . DIRECTORY_SEPARATOR;
        $file .= 'OHAFS_' . $md5;
        $this->dir->createPath(
            $this->path,
            substr($md5, 0, 2) . DIRECTORY_SEPARATOR . substr($md5, 2, 2)
        );
        $handle = fopen($file, 'w');
        fwrite($handle, (time() + 100) . PHP_EOL . 'a;x/sdr');
        fclose($handle);
        $adapter = clone $this->adapter;
        $this->assertNull($adapter->get($name));

        $name = rand(0, 999);
        $md5 = md5($name);
        $file = $this->path . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 0, 2) . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 2, 2) . DIRECTORY_SEPARATOR;
        $file .= 'OHAFS_' . $md5;
        $this->dir->createPath(
            $this->path,
            substr($md5, 0, 2) . DIRECTORY_SEPARATOR . substr($md5, 2, 2)
        );
        $handle = fopen($file, 'w');
        fwrite($handle, 'abc');
        fclose($handle);
        $adapter = clone $this->adapter;
        $this->assertNull($adapter->get($name));

        $name = rand(0, 999);
        $md5 = md5($name);
        $file = $this->path . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 0, 2) . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 2, 2) . DIRECTORY_SEPARATOR;
        $file .= 'OHAFS_' . $md5;
        $this->dir->createPath(
            $this->path,
            substr($md5, 0, 2) . DIRECTORY_SEPARATOR . substr($md5, 2, 2)
        );
        $handle = fopen($file, 'w');
        fwrite($handle, 'abc');
        fclose($handle);
        $adapter = clone $this->adapter;
        $this->assertFalse($adapter->renew($name, 100));

        $name = rand(0, 999);
        $md5 = md5($name);
        $file = $this->path . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 0, 2) . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 2, 2) . DIRECTORY_SEPARATOR;
        $file .= 'OHAFS_' . $md5;
        $this->dir->createPath(
            $this->path,
            substr($md5, 0, 2) . DIRECTORY_SEPARATOR . substr($md5, 2, 2)
        );
        $handle = fopen($file, 'w');
        fwrite($handle, (time() + 100) . PHP_EOL . 'abc');
        fclose($handle);
        $adapter = clone $this->adapter;
        $this->assertFalse($adapter->renew($name, 100));

        $name = rand(0, 999);
        $md5 = md5($name);
        $file = $this->path . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 0, 2) . DIRECTORY_SEPARATOR;
        $file .= substr($md5, 2, 2) . DIRECTORY_SEPARATOR;
        $file .= 'OHAFS_' . $md5;
        $this->dir->createPath(
            $this->path,
            substr($md5, 0, 2) . DIRECTORY_SEPARATOR . substr($md5, 2, 2)
        );
        $handle = fopen($file, 'w');
        fwrite($handle, 0);
        fclose($handle);
        $adapter = clone $this->adapter;
        $this->assertNull($adapter->get($name));
    }

    public function testCannotCreatePath()
    {
        $path = $this->path . DIRECTORY_SEPARATOR . 'nowrite';
        @mkdir($path);
        chmod($path, 666);
        /** @noinspection PhpUnhandledExceptionInspection */
        $adapter = new AdapterFileSystem(['path' => $path]);
        $this->assertFalse($adapter->set('abc', 'def', 100));
        rmdir($path);
        $this->assertFalse($adapter->renew('abc', 100));
    }

    public function testSetRenew()
    {
        $this->assertTrue($this->adapter->set($this->name, 'foobar', 10));
        $this->assertTrue($this->adapter->renew($this->name, 10));
        $this->assertFalse($this->adapter->renew(md5(microtime()), 100));
    }

    public function testSetRemove()
    {
        $this->assertTrue($this->adapter->set('foo', 'foobar', 10));
        $this->assertTrue($this->adapter->remove('foo'));
        $this->assertFalse($this->adapter->remove(md5(time() + rand(0, 99))));
    }

    public function testHas()
    {
        $this->assertTrue($this->adapter->set($this->name, 'foobar', 10));
        $this->assertTrue($this->adapter->has($this->name));
        $this->assertEquals('foobar', $this->adapter->get($this->name));
        $this->assertTrue($this->adapter->remove($this->name));
        $this->assertFalse($this->adapter->has($this->name));
    }

    public function testRemove()
    {
        $this->assertTrue($this->adapter->set($this->name, 'foobar', 10));
        $this->assertTrue($this->adapter->remove($this->name));
        $this->assertFalse($this->adapter->has($this->name));
    }

    public function testFlush()
    {
        $this->assertTrue($this->adapter->flush());
    }
}
