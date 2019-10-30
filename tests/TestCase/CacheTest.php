<?php
namespace Tests\TestCase;

use Tests\TestCase;
use SeoAnalyzer\Cache;
use Symfony\Component\Cache\Exception\InvalidArgumentException;

class CacheTest extends TestCase
{
    public function testRememberPass()
    {
        $cache = new Cache();
        $cache->adapter->delete('testCacheKey');
        $value = $cache->remember('testCacheKey', function () {
            return 'initialValue';
        });
        $this->assertEquals('initialValue', $value);
        $valueCached = $cache->remember('testCacheKey', function () {
            return 'newValue';
        });
        $this->assertEquals('initialValue', $valueCached);
        $cache->adapter->delete('testCacheKey');
        $valueRefreshed = $cache->remember('testCacheKey', function () {
            return 'newValueOneMoreTime';
        });
        $this->assertEquals('newValueOneMoreTime', $valueRefreshed);
    }

    public function testRememberPassOninvalidKey()
    {
        $cache = new Cache();
        $value = $cache->remember('@', function () {
            return 'someTestValue';
        });
        $this->assertEquals('someTestValue', $value);
        $value = $cache->remember('@', function () {
            return 'someNewTestValue';
        });
        $this->assertEquals('someNewTestValue', $value);
    }

    public function testGetFailOnInvalidKey()
    {
        $cache = new Cache();
        $this->assertFalse($cache->get('@'));
    }

    public function testSetFailOnInvalidKey()
    {
        $cache = new Cache();
        $this->assertFalse($cache->set('@', 'value'));
    }

    public function testGetFailOnInvalidArgumentExceptionFromAdapter()
    {
        $cache = new Cache(TestCase\Metric\Mock\TestCacheAdapter::class);
        $cache->set('test', 'test');
        $this->assertFalse($cache->get('test'));
    }
}
