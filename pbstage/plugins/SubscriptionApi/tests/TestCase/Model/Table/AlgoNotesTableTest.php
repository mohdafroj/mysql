<?php
namespace SubscriptionApi\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use SubscriptionApi\Model\Table\AlgoNotesTable;

/**
 * SubscriptionApi\Model\Table\AlgoNotesTable Test Case
 */
class AlgoNotesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \SubscriptionApi\Model\Table\AlgoNotesTable
     */
    public $AlgoNotes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.subscription_api.algo_notes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('AlgoNotes') ? [] : ['className' => AlgoNotesTable::class];
        $this->AlgoNotes = TableRegistry::get('AlgoNotes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AlgoNotes);

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
