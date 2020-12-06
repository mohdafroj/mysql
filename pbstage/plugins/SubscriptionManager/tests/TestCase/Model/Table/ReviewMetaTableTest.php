<?php
namespace SubscriptionManager\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use SubscriptionManager\Model\Table\ReviewMetaTable;

/**
 * SubscriptionManager\Model\Table\ReviewMetaTable Test Case
 */
class ReviewMetaTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \SubscriptionManager\Model\Table\ReviewMetaTable
     */
    public $ReviewMeta;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.subscription_manager.review_meta',
        'plugin.subscription_manager.reviews',
        'plugin.subscription_manager.customers',
        'plugin.subscription_manager.addresses',
        'plugin.subscription_manager.memberships',
        'plugin.subscription_manager.orders',
        'plugin.subscription_manager.locations',
        'plugin.subscription_manager.customer_wallets',
        'plugin.subscription_manager.customer_logs',
        'plugin.subscription_manager.product_prices',
        'plugin.subscription_manager.couriers',
        'plugin.subscription_manager.payment_methods',
        'plugin.subscription_manager.invoices',
        'plugin.subscription_manager.invoice_details',
        'plugin.subscription_manager.order_details',
        'plugin.subscription_manager.products',
        'plugin.subscription_manager.product_categories',
        'plugin.subscription_manager.categories',
        'plugin.subscription_manager.parent_categories',
        'plugin.subscription_manager.product_images',
        'plugin.subscription_manager.carts',
        'plugin.subscription_manager.product_notes',
        'plugin.subscription_manager.url_rewrite',
        'plugin.subscription_manager.wishlists',
        'plugin.subscription_manager.brands',
        'plugin.subscription_manager.category_brands',
        'plugin.subscription_manager.order_comments'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ReviewMeta') ? [] : ['className' => ReviewMetaTable::class];
        $this->ReviewMeta = TableRegistry::get('ReviewMeta', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ReviewMeta);

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
