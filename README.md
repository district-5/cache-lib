District5 - CacheLib
====================

CacheLib is a flexible caching library for PHP.

Using Composer
--------------

Example Composer file contents:

```
composer require district5/cache-lib
```

Testing
-------

Tests against adapters will only occur if the extension is loaded (for example APC/Memcache/Memcached).

`$ composer install`

`$ ./vendor/bin/phpunit`

Quick Start
-----------

CacheLib exposes the following methods for Caching:

 * `APC` / `APCU` - The 
 * `Memcache`
 * `Memcached`
 * `FileSystem`
 
 To find out more about how to use the adapters, see the `tests/CacheLibTests/Adapters` directory.

In addition to this, there is a `AdapterNull` which you can use for test (or development) purposes.

Getting APCU working on a Mac
-----------------------------

```shell
$ pecl install apcu
```

Add this to your php.ini file (`/usr/local/etc/php/7.4/php.ini`)

```shell
apc.enabled=on
apc.shm_size=64M
apc.enable_cli=on
```