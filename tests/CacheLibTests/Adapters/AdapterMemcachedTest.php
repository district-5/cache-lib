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

use District5\CacheLib\Adapters\AdapterMemcached;
use PHPUnit\Framework\TestCase;

/**
 * Class AdapterMemcachedTest
 * @package District5Tests\CacheLibTests\Adapters
 */
class AdapterMemcachedTest extends TestCase
{
    /**
     * @var AdapterMemcached
     */
    private $adapter = null;

    private $name = null;

    public function setUp()
    {
        if (!class_exists('\Memcached')) {
            $this->markTestSkipped(
                'Memcached class is not present. Try installing Memcached (and the PHP driver)'
            );
            return;
        }
        $this->name = md5(__CLASS__);
        $cacheConfig = [
            'prefix' => 'TEST_',
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211,
                    'timeout' => 60,
                    'weight' => 1
                ]
            ]
        ];
        $this->adapter = new AdapterMemcached($cacheConfig);
        $this->adapter->flush();
    }

    public function testSetGet()
    {
        $this->assertTrue($this->adapter->set($this->name, 'foo', 10));
        $this->assertNull($this->adapter->get('foobarfoo'));
        $this->assertEquals('foo', $this->adapter->get($this->name));
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

    public function testSetRenew()
    {
        $this->assertTrue($this->adapter->set($this->name, 'bar', 10));
        $this->assertTrue($this->adapter->renew($this->name, 10));
        $this->assertFalse($this->adapter->renew(md5(microtime()), 100));
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
        $this->assertTrue($this->adapter->set($this->name, 'barfoo', 10));
        $this->assertTrue($this->adapter->remove($this->name));
        $this->assertFalse($this->adapter->has($this->name));
    }
}
