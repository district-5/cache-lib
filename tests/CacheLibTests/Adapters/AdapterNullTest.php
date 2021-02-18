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

namespace District5Tests\CacheLibTests\Adapters;

use District5\CacheLib\Adapters\AdapterNull;
use PHPUnit\Framework\TestCase;

/**
 * Class AdapterNullTest
 * @package District5Tests\CacheLibTests\Adapters
 */
class AdapterNullTest extends TestCase
{
    public function testSet()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->set('foo', 'bar'));
    }

    public function testSetIfNotExists()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->setIfNotExists('foo', 'bar'));
    }

    public function testGet()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->get('foo'));
    }

    public function testHas()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->has('foo'));
    }

    public function testRenew()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->renew('foo', 3600));
    }

    public function testRemove()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->remove('foo'));
    }

    public function testFlush()
    {
        $instance = new AdapterNull();
        $this->assertFalse($instance->flush());
    }
}

