<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ShipvendorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ShipvendorsTable Test Case
 */
class ShipvendorsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ShipvendorsTable
     */
    public $Shipvendors;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.shipvendors',
        'app.shipvendor_pincodes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Shipvendors') ? [] : ['className' => ShipvendorsTable::class];
        $this->Shipvendors = TableRegistry::get('Shipvendors', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Shipvendors);

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
}
