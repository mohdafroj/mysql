<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SystemsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SystemsTable Test Case
 */
class SystemsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SystemsTable
     */
    public $Systems;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.systems'
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
}
