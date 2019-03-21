<?php

namespace Alvarium\CarrotCake\Model\Behavior;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PublisherBehavior extends Behavior
{
    public $connection = null;
    protected $channel = null;
    protected $_defaultConfig = [
        'routes' => [
            'create' => 'created',
            'update' => 'updated',
            'delete' => 'deleted',
        ],
        'exchange' => '',
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);

        $settings = array_replace_recursive(
            $this->_defaultConfig,
            Configure::read('rabbit.server'),
            Configure::read('rabbit.behavior'),
            $config
        );

        $this->setConfig($settings);
    }

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $exchange = lcfirst($event->getSubject()->getAlias());

        $route = $this->getConfig('routes')['create'];
        if (!$entity->isNew()) {
            $route = $this->getConfig('routes')['update'];
        }

        $this->publish($entity, $route, 'json', $exchange);
    }

    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $exchange = lcfirst($event->getSubject()->getAlias());

        $route = $this->getConfig('routes')['delete'];

        $this->publish($entity, $route, 'json', $exchange);
    }

    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = new AMQPStreamConnection(
                $this->getConfig('host'),
                $this->getConfig('port'),
                $this->getConfig('user'),
                $this->getConfig('password'),
                $this->getConfig('vhost')
            );
        }

        return $this->connection;
    }

    public function setConnection(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    protected function prepareMessage($message, string $type): AMQPMessage
    {
        if ($type == 'auto' && is_array($message)) {
            $type = 'json';
        }

        switch ($type) {
            case 'json':
                $message = json_encode($message);
                $type = 'application/json';
                break;

            default:
                $message = (string)$message;
                $type = 'text/plain';
                break;
        }

        return new AMQPMessage($message, [
            'content_type' => $type,
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'timestamp' => time(),
        ]);
    }

    public function publish($data, string $route, string $type = 'auto', string $exchange = null)
    {
        $exchange = $this->getConfig('exchange') ?: $exchange;

        if (method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        $connection = $this->getConnection();
        $this->channel = $connection->channel();

        $this->channel->exchange_declare(
            $exchange,
            $this->getConfig('type'),
            false,
            true,
            false
        );

        $this->channel->basic_publish(
            $this->prepareMessage($data, $type),
            $exchange,
            $route
        );
    }
}
