<?php
namespace Alvarium\CarrotCake\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
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

        $this->Members->addBehaviors([
            'Alvarium/CarrotCake.Publisher',
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
        unset($this->Behavior);
        unset($this->Members);

        parent::tearDown();
    }

    /**
     * Test behavior initialization
     *
     * @return void
     */
    public function testInitialize()
    {
        // Should load settings set via config file
        Configure::write('rabbit.behavior.exchange', 'test');
        $this->Behavior->initialize([]);
        $this->assertTextEquals('test', $this->Behavior->getConfig('exchange'));

        // Same if we spaceify it directly to the initialize method
        Configure::write('rabbit.behavior.exchange', '');
        $this->Behavior->initialize(['exchange' => 'test']);
        $this->assertTextEquals('test', $this->Behavior->getConfig('exchange'));
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
