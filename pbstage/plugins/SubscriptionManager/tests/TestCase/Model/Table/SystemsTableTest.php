<?php
namespace SubscriptionManager\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use SubscriptionManager\Model\Table\SystemsTable;

/**
 * SubscriptionManager\Model\Table\SystemsTable Test Case
 */
class SystemsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \SubscriptionManager\Model\Table\SystemsTable
     */
    public $Systems;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.subscription_manager.systems'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Systems') ? [] : ['className' => SystemsTable::class];
        $this->Systems = TableRegistry::get('Systems', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Systems);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test defaultConnectionName method
     *
     * @return void
     */
    public function testDefaultConnectionName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
