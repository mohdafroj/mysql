<?php
namespace SubscriptionManager\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use SubscriptionManager\Model\Table\AlgoProductsTable;

/**
 * SubscriptionManager\Model\Table\AlgoProductsTable Test Case
 */
class AlgoProductsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \SubscriptionManager\Model\Table\AlgoProductsTable
     */
    public $AlgoProducts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.subscription_manager.algo_products'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('AlgoProducts') ? [] : ['className' => AlgoProductsTable::class];
        $this->AlgoProducts = TableRegistry::get('AlgoProducts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AlgoProducts);

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
