# php-giantbomb

[![Packagist](https://img.shields.io/packagist/v/dborsatto/php-giantbomb.svg)](https://packagist.org/packages/dborsatto/php-giantbomb)
[![Packagist](https://img.shields.io/packagist/dt/dborsatto/php-giantbomb.svg)](https://packagist.org/packages/dborsatto/php-giantbomb)
[![Packagist](https://img.shields.io/packagist/l/dborsatto/php-giantbomb.svg)](https://packagist.org/packages/dborsatto/php-giantbomb)

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
$config = new DBorsatto\GiantBomb\Configuration($apiKey);
$client = new DBorsatto\GiantBomb\Client($config);

// OPTIONAL: use a PSR-16 simple cache pool
$cache = new Cache\Adapter\PHPArray\ArrayCachePool();
$client = new DBorsatto\GiantBomb\Client($config, $cache);

// Standard query creation process
$query = DBorsatto\GiantBomb\Query::create()
    ->addFilterBy('name', 'Uncharted')
    ->sortBy('original_release_date', 'asc')
    ->setFieldList(['id', 'name', 'deck'])
    ->setParameter('limit', '100')
    ->setParameter('offset', '0');
$games = $client->find('Game', $query);
echo count($games)." Game objects loaded\n";

// These two options are equivalent
$game = $client->findOne('Game', Query::createForResourceId('3030-22420'));
// The findWithResourceID method is just a shortcut
$game = $client->findWithResourceID('Game', '3030-22420');
echo $game->get('name')." object loaded\n";

// These two options are equivalent
$query = DBorsatto\GiantBomb\Query::create()
    ->setParameter('query', 'Uncharted')
    ->setParameter('resources', 'game,franchise');
$results = $client->find('Search', $query);
// The search method is just a shortcut
$results = $client->search('Uncharted', 'game,franchise');
echo count($results)." Search objects loaded\n";
```

For the full option list visit [GiantBomb's API doc](http://www.giantbomb.com/api/documentation).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
