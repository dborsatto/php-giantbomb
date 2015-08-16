<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../api_key.php';

// Parses the default configuration
$configFile = Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__.'/../api_config.yml'));

// Creates a Config object and passes to the Manager
$config = new dborsatto\GiantBomb\Config($apiKey, $configFile);
$manager = new dborsatto\GiantBomb\Manager($config);

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
    ->setParameter('query', $string)
    ->setParameter('resources', 'game,franchise')
    ->find();
$results = $manager->search('Uncharted', 'game,franchise');
