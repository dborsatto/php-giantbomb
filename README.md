# php-giantbomb

[![Latest Stable Version](https://poser.pugx.org/dborsatto/php-giantbomb/v/stable)](https://packagist.org/packages/dborsatto/php-giantbomb)
[![Build Status](https://secure.travis-ci.org/dborsatto/php-giantbomb.png?branch=master)](http://travis-ci.org/dborsatto/php-giantbomb)

This is a library that acts as a wrapper for GiantBomb's API.

## Install

Via Composer

``` bash
$ composer require dborsatto/php-giantbomb
```

## Usage
```php

$apiKey = 'YouApiKey';

// Create a Config object and pass it to the Client
$config = new DBorsatto\GiantBomb\Config($apiKey);
$client = new DBorsatto\GiantBomb\Client($config);

// OPTIONAL: use a cache driver
// Anything that extends Doctrine\Common\Cache\CacheProvider will work
$memcached = new Memcached();
$memcached->addServer('127.0.0.1', 11211);
$cache = new Doctrine\Common\Cache\MemcachedCache();
$cache->setMemcached($memcached);
$client->setCacheProvider($cache);

// Alternatively you can pass a CacheProvider instance as the second parameter of the Client's constructor
// $client = new DBorsatto\GiantBomb\Client($config, $cache);
// If no CacheProvider is configured, Doctrine\Common\Cache\VoidCache will be used
// You can still flush the current cache by invoking
// $client->getCacheProvider()->flush();

// Standard query creation process
$games = $client->getRepository('Game')->query()
    ->addFilterBy('name', 'Uncharted')
    ->sortBy('original_release_date', 'asc')
    ->setFieldList(array('id', 'name', 'deck'))
    ->setParameter('limit', 100)
    ->setParameter('offset', 0)
    ->find();
echo count($games)." Game objects loaded\n";

// These options are all equivalent
$game = $client->getRepository('Game')
    ->query()
    ->setResourceId('3030-22420')
    ->findOne();
$game = $client->query('Game')
    ->setResourceId('3030-22420')
    ->findOne();
$game = $client->findOne('Game', '3030-22420');
echo $game->get('name')." object loaded\n";

// These options are equivalent
$results = $client->getRepository('Search')
    ->query()
    ->setParameter('query', 'Uncharted')
    ->setParameter('resources', 'game,franchise')
    ->find();
$results = $client->search('Uncharted', 'game,franchise');
echo count($results)." Search objects loaded\n";
```

For the full option list visit [GiantBomb's API doc](http://www.giantbomb.com/api/documentation).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
