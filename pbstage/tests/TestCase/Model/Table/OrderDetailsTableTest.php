<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OrderDetailsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OrderDetailsTable Test Case
 */
class OrderDetailsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\OrderDetailsTable
     */
    public $OrderDetails;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.order_details',
        'app.orders',
        'app.customers',
        'app.addresses',
        'app.memberships',
        'app.carts',
        'app.payment_methods',
        'app.order_comments',
        'app.products',
        'app.products_categories',
        'app.products_images',
        'app.reviews',
        'app.products_notes',
        'app.url_rewrite',
        'app.wishlists',
        'app.brands',
        'app.categories_brands'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('OrderDetails') ? [] : ['className' => OrderDetailsTable::class];
        $this->OrderDetails = TableRegistry::get('OrderDetails', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OrderDetails);

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
