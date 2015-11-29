<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../api_key.php';

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

// Alternatively you can pass a CacheProvider as the second parameter of the Client's constructor
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
