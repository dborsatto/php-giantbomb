# php-giantbomb

[![Software License][https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square]](LICENSE.md)
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

// Creates a Config object and passes to the Manager
$config = new DBorsatto\GiantBomb\Config($apiKey);
$manager = new DBorsatto\GiantBomb\Manager($config);

// Standard query creation process
$games = $manager->getRepository('Game')->query()
    ->addFilterBy('name', 'Uncharted')
    ->sortBy('original_release_date', 'asc')
    ->setFieldList(array('id', 'name', 'deck'))
    ->setParameter('limit', 100)
    ->setParameter('offset', 0)
    ->find();

// These methods are all equivalent
$game = $manager->getRepository('Game')
    ->query()
    ->setResourceId('3030-22420')
    ->findOne();
$game = $manager->query('Game')
    ->setResourceId('3030-22420')
    ->findOne();
$game = $manager->findOne('Game', '3030-22420');

// These methods are equivalent
$results = $manager->getRepository('Search')
    ->query()
    ->setParameter('query', 'Uncharted')
    ->setParameter('resources', 'game,franchise')
    ->find();
$results = $manager->search('Uncharted', 'game,franchise');
```

For the full option list visit [GiantBomb's API doc](http://www.giantbomb.com/api/documentation).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
