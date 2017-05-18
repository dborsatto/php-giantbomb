# php-giantbomb

[![Packagist](https://img.shields.io/packagist/v/dborsatto/php-giantbomb.svg)](https://packagist.org/packages/dborsatto/php-giantbomb)
[![Packagist](https://img.shields.io/packagist/dt/dborsatto/php-giantbomb.svg)](https://packagist.org/packages/dborsatto/php-giantbomb)
[![Travis](https://img.shields.io/travis/dborsatto/php-giantbomb.svg)](https://travis-ci.org/dborsatto/php-giantbomb)
[![Packagist](https://img.shields.io/packagist/l/dborsatto/php-giantbomb.svg)](https://packagist.org/packages/dborsatto/php-giantbomb)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e630e521-fd39-4ac2-b994-5149d6ffaca4/mini.png)](https://insight.sensiolabs.com/projects/e630e521-fd39-4ac2-b994-5149d6ffaca4)
[![Code Climate](https://codeclimate.com/github/dborsatto/php-giantbomb/badges/gpa.svg)](https://codeclimate.com/github/dborsatto/php-giantbomb)
[![Test Coverage](https://codeclimate.com/github/dborsatto/php-giantbomb/badges/coverage.svg)](https://codeclimate.com/github/dborsatto/php-giantbomb/coverage)

This is a library that acts as a wrapper for GiantBomb's API.

## Install

Via Composer

``` bash
$ composer require dborsatto/php-giantbomb
```

## Usage

``` php

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
