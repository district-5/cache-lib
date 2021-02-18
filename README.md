District5 - CacheLib
====

CacheLib is a flexible caching library for PHP.

Using Composer
----

Example Composer file contents:

```
composer require district5/cache-lib
```

Testing
----

Tests against adapters will only occur if the extension is loaded (for example APC/Memcache/Memcached).

`$ composer install`

`$ ./vendor/bin/phpunit`

Quick Start
----

CacheLib exposes the following methods for Caching:

 * `APC` / `APCU` - The 
 * `Memcache`
 * `Memcached`
 * `FileSystem`
 
 To find out more about how to use the adapters, see the `tests/CacheLibTests/Adapters` directory.

In addition to this, there is a `AdapterNull` which you can use for test (or development) purposes.

APC Adapter
----

The APC adapter will either use `apc` or `apcu` depending on availability.

```php
<?php
$key = 'name';
$value = 'Joe Bloggs';

$adapter = new \District5\CacheLib\Adapters\AdapterApc(
    [
        'prefix' => '' // default
    ]
);
$adapter->get($key, $default = null); // returns false or mixed
$adapter->set($key, $value, $ttl = 86400); // returns bool
$adapter->has($key); // returns bool
$adapter->renew($key, $ttl); // returns bool
$adapter->remove($key); // returns bool
$adapter->setIfNotExists($key, $value, $ttl = 86400); // returns bool
$adapter->flush(); // returns bool
```

FileSystem Adapter
----

The APC adapter will either use `apc` or `apcu` depending on availability.

```php
<?php
$key = 'name';
$value = 'Joe Bloggs';

$adapter = new \District5\CacheLib\Adapters\AdapterFileSystem(
    [
        'prefix' => '', // optional
        'path' => '/some/writable/directory'
    ]
);
$adapter->get($key, $default = null); // returns false or mixed
$adapter->set($key, $value, $ttl = 86400); // returns bool
$adapter->has($key); // returns bool
$adapter->renew($key, $ttl); // returns bool
$adapter->remove($key); // returns bool
$adapter->setIfNotExists($key, $value, $ttl = 86400); // returns bool
$adapter->flush(); // returns bool
```

Memcache Adapter
----

The memcache adapter supports multiple memcache servers like the memcached adapter

```php
<?php
$key = 'name';
$value = 'Joe Bloggs';

$adapter = new \District5\CacheLib\Adapters\AdapterMemcache(
    [
        'prefix' => '', // optional
        'servers' => [
            [
                'host' => 'a-host-name',
                'port' => 11211,
                'timeout' => 60,
                'weight' => 1
            ],
            [
                'host' => 'another-host-name',
                'port' => 11211,
                'timeout' => 60,
                'weight' => 1
            ]
        ]
            
    ]
);
$adapter->get($key, $default = null); // returns false or mixed
$adapter->set($key, $value, $ttl = 86400); // returns bool
$adapter->has($key); // returns bool
$adapter->renew($key, $ttl); // returns bool
$adapter->remove($key); // returns bool
$adapter->setIfNotExists($key, $value, $ttl = 86400); // returns bool
$adapter->flush(); // returns bool
```

Memcached Adapter
----

The memcached adapter supports multiple servers, like the memcache adapter. 

```php
<?php
$key = 'name';
$value = 'Joe Bloggs';

$adapter = new \District5\CacheLib\Adapters\AdapterMemcached(
    [
        'prefix' => '', // optional
        'persistent_id' => '', // optional
        'servers' => [
            [
                'host' => 'a-host-name',
                'port' => 11211,
                'weight' => 1
            ],
            [
                'host' => 'another-host-name',
                'port' => 11211,
                'weight' => 1
            ]
        ]
            
    ]
);
$adapter->get($key, $default = null); // returns false or mixed
$adapter->set($key, $value, $ttl = 86400); // returns bool
$adapter->has($key); // returns bool
$adapter->renew($key, $ttl); // returns bool
$adapter->remove($key); // returns bool
$adapter->setIfNotExists($key, $value, $ttl = 86400); // returns bool
$adapter->flush(); // returns bool
```

Null Adapter (for tests)
----

The `null` adapter returns false for all methods. It's useful for some test cases.

```php
<?php
$key = 'name';
$value = 'Joe Bloggs';

$adapter = new \District5\CacheLib\Adapters\AdapterNull([]);
$adapter->get($key, $default = null); // returns false
$adapter->set($key, $value, $ttl = 86400); // returns false
$adapter->has($key); // returns false
$adapter->renew($key, $ttl); // returns false
$adapter->remove($key); // returns false
$adapter->setIfNotExists($key, $value, $ttl = 86400); // returns false
$adapter->flush(); // returns false
```

Getting APCU working on a Mac
----

```shell
$ pecl install apcu
```

Add this to your php.ini file (`/usr/local/etc/php/7.4/php.ini`)

```shell
apc.enabled=on
apc.shm_size=64M
apc.enable_cli=on
```