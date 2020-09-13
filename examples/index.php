<?php

use DBorsatto\GiantBomb\Client;
use DBorsatto\GiantBomb\Configuration;

require __DIR__.'/../vendor/autoload.php';

$apiKey = '...';

$config = new Configuration($apiKey);
$client = new Client($config);

$uncharted2 = $client->findWithResourceID('Game', '3030-22420');
var_dump($uncharted2->get('name'));
