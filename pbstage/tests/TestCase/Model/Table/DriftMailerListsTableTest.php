<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DriftMailerListsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DriftMailerListsTable Test Case
 */
class DriftMailerListsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\DriftMailerListsTable
     */
    public $DriftMailerLists;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.drift_mailer_lists',
        'app.drift_mailers'
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
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
