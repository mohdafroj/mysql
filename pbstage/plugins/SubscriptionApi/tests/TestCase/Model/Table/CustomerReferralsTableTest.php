<?php
namespace SubscriptionApi\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use SubscriptionApi\Model\Table\CustomerReferralsTable;

/**
 * SubscriptionApi\Model\Table\CustomerReferralsTable Test Case
 */
class CustomerReferralsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \SubscriptionApi\Model\Table\CustomerReferralsTable
     */
    public $CustomerReferrals;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.subscription_api.customer_referrals',
        'plugin.subscription_api.customers',
        'plugin.subscription_api.addresses',
        'plugin.subscription_api.memberships',
        'plugin.subscription_api.orders',
        'plugin.subscription_api.locations',
        'plugin.subscription_api.customer_wallets',
        'plugin.subscription_api.customer_logs',
        'plugin.subscription_api.product_prices',
        'plugin.subscription_api.couriers',
        'plugin.subscription_api.payment_methods',
        'plugin.subscription_api.invoices',
        'plugin.subscription_api.invoice_details',
        'plugin.subscription_api.order_details',
        'plugin.subscription_api.products',
        'plugin.subscription_api.product_categories',
        'plugin.subscription_api.categories',
        'plugin.subscription_api.parent_categories',
        'plugin.subscription_api.product_images',
        'plugin.subscription_api.reviews',
        'plugin.subscription_api.carts',
        'plugin.subscription_api.product_notes',
        'plugin.subscription_api.url_rewrite',
        'plugin.subscription_api.wishlists',
        'plugin.subscription_api.brands',
        'plugin.subscription_api.category_brands',
        'plugin.subscription_api.order_comments',
        'plugin.subscription_api.referrals'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CustomerReferrals') ? [] : ['className' => CustomerReferralsTable::class];
        $this->CustomerReferrals = TableRegistry::get('CustomerReferrals', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CustomerReferrals);

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
