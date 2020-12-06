<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProductsCategoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProductsCategoriesTable Test Case
 */
class ProductsCategoriesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ProductsCategoriesTable
     */
    public $ProductsCategories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.products_categories',
        'app.products',
        'app.products_images',
        'app.reviews',
        'app.customers',
        'app.addresses',
        'app.memberships',
        'app.orders',
        'app.payment_methods',
        'app.order_details',
        'app.order_comments',
        'app.carts',
        'app.products_notes',
        'app.url_rewrite',
        'app.wishlists',
        'app.brands',
        'app.categories_brands',
        'app.categories'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ProductsCategories') ? [] : ['className' => ProductsCategoriesTable::class];
        $this->ProductsCategories = TableRegistry::get('ProductsCategories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ProductsCategories);

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
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
