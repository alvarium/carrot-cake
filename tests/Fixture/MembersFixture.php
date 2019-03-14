<?php
namespace Alvarium\CarrotCake\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class MembersFixture extends TestFixture
{
    public $table = 'ck_members';

    /**
     * Fixture fields.
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string', 'null' => false],
        'created' => ['type' => 'datetime', 'null' => true],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

    /**
     * Fixture records.
     *
     * @var array
     */
    public $records = [
        ['name' => 'Dummy'],
        ['name' => 'John Doe'],
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $created = date('Y-m-d H:i:s');
        array_walk($this->records, function (&$record) use ($created) {
            $record += compact('created');
        });
        parent::init();
    }
}
