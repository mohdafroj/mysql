<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ShipvendorPincodesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ShipvendorPincodesTable Test Case
 */
class ShipvendorPincodesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ShipvendorPincodesTable
     */
    public $ShipvendorPincodes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.shipvendor_pincodes',
        'app.shipvendors'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ShipvendorPincodes') ? [] : ['className' => ShipvendorPincodesTable::class];
        $this->ShipvendorPincodes = TableRegistry::get('ShipvendorPincodes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ShipvendorPincodes);

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
