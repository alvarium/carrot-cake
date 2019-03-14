# Kyarottokēki plugin for CakePHP

Carrot Cake (we like to pronounce it in japanese btw: _Kyarottokēki_ [キャロットケーキ]) is a CakePHP plugin for publishing and subscribing to RabbitMQ queues/exchanges.

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require alvarium/carrot-cake
```

## Usage

### Configuring

You can create a defaults settings file by adding a `rabbit.php` file under your config file. The plugin will take that settings as defaults but you'll be able to overwrite them later on each loaded component.

The contents of such file can be with any of the following settings:

~~~php
<?php

return [
  'host' => 'rabbit',
  'port' => 5672,
  'user' => 'guest',
  'password' => 'guest',
  'vhost' => '/',
  'type' => 'direct',
];
~~~

### Publisher

First you'll need to load the behavior in the desired table, when doing so you can override any of the previously defined settings + some specific settings for the behavior, like the used exchange (by default is `tablename`):

~~~php
<?php
namespace App\Model\Table;

use Cake\ORM\Table;
class ArticlesTable extends Table
{
  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->addBehaviors([
      'Alvarium/CarrotCake.Publisher' => [
        'vhost' => '/custom-vhost',
        'exchange' => 'custom_exchange_name',
      ],
    ])
  }
}
~~~

By default the behavior will send payloads to `exchange.route`, where route can be any of:

- created
- updated
- deleted

Of course you can change these too by setting a `routes` key and set your desired names for each route:

~~~php
$this->addBehaviors([
  'Alvarium/CarrotCake.Publisher' => [
    'routes' => [
      'create' => 'published',
      'update' => 'changed',
      'delete' => 'unpublished',
    ],
  ],
])
~~~

## Checklist

- [ ] Publisher
  + [x] Behavior
  + [ ] Component
- [ ] Consumer
  + [ ] Component

## License

[MIT License][license]

Copyright Alvarium.io (c) 2019

[license]: ./LICENSE
