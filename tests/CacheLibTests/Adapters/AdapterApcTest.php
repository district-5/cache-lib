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

use District5\CacheLib\Adapters\AdapterApc;
use District5\CacheLib\Exception\ApcExtensionNotLoaded;
use PHPUnit\Framework\TestCase;

/**
 * Class AdapterApcTest
 * @package District5Tests\CacheLibTests\Adapters
 */
class AdapterApcTest extends TestCase
{
    /**
     * @var AdapterApc
     */
    private $adapter = null;

    private $name = null;

    /**
     * @throws ApcExtensionNotLoaded
     */
    public function setUp()
    {
        if (!extension_loaded('apc') && !extension_loaded('apcu')) {
            $this->markTestSkipped(
                'apc extension is not loaded. Try setting apc.enable_cli=1.'
            );
            return;
        }
        $this->name = md5(__CLASS__);
        $this->adapter = new AdapterApc(['prefix' => 'TEST_']);
        $this->adapter->flush();
    }

    public function testSetGet()
    {
        $this->assertTrue($this->adapter->set($this->name, 'foo', 10));
        $this->assertEquals('foo', $this->adapter->get($this->name));
    }

    // public function testSetGetTtlExpired()
    // {
    //     $this->markTestSkipped('APC will return the value when on the same thread.');
    // }

    public function testSetIfNotExistsGet()
    {
        $random = microtime(true);
        $random = sha1($random);
        $this->assertTrue($this->adapter->setIfNotExists($random, 'foo', 10));
        $this->assertFalse($this->adapter->setIfNotExists($random, 'foo', 10));
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
