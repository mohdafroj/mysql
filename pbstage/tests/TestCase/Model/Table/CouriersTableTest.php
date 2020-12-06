<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CouriersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CouriersTable Test Case
 */
class CouriersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CouriersTable
     */
    public $Couriers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.couriers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Couriers') ? [] : ['className' => CouriersTable::class];
        $this->Couriers = TableRegistry::get('Couriers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Couriers);

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
