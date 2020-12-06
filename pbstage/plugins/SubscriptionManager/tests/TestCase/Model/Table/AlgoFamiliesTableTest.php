<?php
namespace SubscriptionManager\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use SubscriptionManager\Model\Table\AlgoFamiliesTable;

/**
 * SubscriptionManager\Model\Table\AlgoFamiliesTable Test Case
 */
class AlgoFamiliesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \SubscriptionManager\Model\Table\AlgoFamiliesTable
     */
    public $AlgoFamilies;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.subscription_manager.algo_families'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('AlgoFamilies') ? [] : ['className' => AlgoFamiliesTable::class];
        $this->AlgoFamilies = TableRegistry::get('AlgoFamilies', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AlgoFamilies);

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
     * Test defaultConnectionName method
     *
     * @return void
     */
    public function testDefaultConnectionName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
