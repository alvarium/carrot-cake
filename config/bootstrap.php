<?php

use Cake\Core\Configure;

$rabbit = [
    'host' => 'rabbit',
    'port' => 5672,
    'user' => 'guest',
    'password' => 'guest',
    'vhost' => '/',
    'type' => 'direct',
];

Configure::write('rabbit', $rabbit);

if (!defined('CONFIG')) {
    return;
}

if (file_exists(CONFIG . 'rabbit.php')) {
    Configure::load('rabbit');
}
