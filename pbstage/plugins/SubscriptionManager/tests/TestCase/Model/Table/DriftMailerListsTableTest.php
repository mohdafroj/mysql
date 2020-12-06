<?php
namespace SubscriptionManager\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use SubscriptionManager\Model\Table\DriftMailerListsTable;

/**
 * SubscriptionManager\Model\Table\DriftMailerListsTable Test Case
 */
class DriftMailerListsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \SubscriptionManager\Model\Table\DriftMailerListsTable
     */
    public $DriftMailerLists;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.subscription_manager.drift_mailer_lists'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('DriftMailerLists') ? [] : ['className' => DriftMailerListsTable::class];
        $this->DriftMailerLists = TableRegistry::get('DriftMailerLists', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DriftMailerLists);

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
