<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../api_key.php';

// Creates a Config object and passes to the Client
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
