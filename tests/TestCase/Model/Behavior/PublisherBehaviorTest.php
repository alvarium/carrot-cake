<?php
namespace Alvarium\CarrotCake\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Alvarium\CarrotCake\Model\Behavior\PublisherBehavior;

/**
 * CarrotCake\Model\Behavior\PublisherBehavior Test Case
 */
class PublisherBehaviorTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \CarrotCake\Model\Behavior\PublisherBehavior
     */
    public $Publisher;

    public $fixtures = [
        'plugin.Alvarium/CarrotCake.Members',
    ];

    /**
     * Runs before each test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Members = TableRegistry::get('Alvarium/CarrotCake.Members', [
            'table' => 'ck_members'
        ]);

        $this->Behavior = $this->Members->behaviors()->Publisher;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Publisher);

        parent::tearDown();
    }

    /**
     * Test save.
     *
     * @return void
     */
    public function testSave()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test update.
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete.
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
