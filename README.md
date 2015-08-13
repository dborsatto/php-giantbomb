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

// Creates a Config object and passes it to the Client
$config = new DBorsatto\GiantBomb\Config($apiKey);
$client = new DBorsatto\GiantBomb\Client($config);

// Standard query creation process
$games = $client->getRepository('Game')->query()
    ->addFilterBy('name', 'Uncharted')
    ->sortBy('original_release_date', 'asc')
    ->setFieldList(array('id', 'name', 'deck'))
    ->setParameter('limit', 100)
    ->setParameter('offset', 0)
    ->find();

// These options are all equivalent
$game = $client->getRepository('Game')
    ->query()
    ->setResourceId('3030-22420')
    ->findOne();
$game = $client->query('Game')
    ->setResourceId('3030-22420')
    ->findOne();
$game = $client->findOne('Game', '3030-22420');

// These options are equivalent
$results = $client->getRepository('Search')
    ->query()
    ->setParameter('query', 'Uncharted')
    ->setParameter('resources', 'game,franchise')
    ->find();
$results = $client->search('Uncharted', 'game,franchise');
```

For the full option list visit [GiantBomb's API doc](http://www.giantbomb.com/api/documentation).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
