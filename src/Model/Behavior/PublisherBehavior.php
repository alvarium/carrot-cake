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
        'implementedMethods' => [
            'afterSave' => 'save',
            'afterDelete' => 'delete',
        ],
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
            Configure::read('rabbit.behavior'),
            $config
        );

        $this->setConfig($settings);
    }

    public function save(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $route = $this->getConfig('routes')['create'];
        if (!$entity->isNew()) {
            $route = $this->getConfig('routes')['update'];
        }

        $this->publish($entity, $route);
    }

    public function delete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $route = $this->getConfig('routes')['delete'];

        $this->publish($entity, $route);
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

    protected function publish($data, string $route, string $type = 'auto'): void
    {
        if (method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        $this->connection = new AMQPStreamConnection(
            $this->getConfig('host'),
            $this->getConfig('port'),
            $this->getConfig('user'),
            $this->getConfig('password'),
            $this->getConfig('vhost')
        );
        $this->channel = $this->connection->channel();

        $this->channel->exchange_declare(
            $this->getConfig('exchange'),
            $this->getConfig('type'),
            false,
            true,
            false
        );

        $this->channel->basic_publish(
            $this->prepareMessage($data, $type),
            $this->getConfig('exchange'),
            $route
        );
    }
}
