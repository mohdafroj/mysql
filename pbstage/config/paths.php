<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * These defines should only be edited if you have cake installed in
 * a directory layout other than the way it is distributed.
 * When using custom settings be sure to use the DS and do not add a trailing DS.
 */

/**
 * The full path to the directory which holds "src", WITHOUT a trailing DS.
 */
define('ROOT', dirname(__DIR__));

/**
 * The actual directory name for the application directory. Normally
 * named 'src'.
 */
define('APP_DIR', 'src');

/**
 * Path to the application's directory.
 */
define('APP', ROOT . DS . APP_DIR . DS);

/**
 * Path to the config directory.
 */
define('CONFIG', ROOT . DS . 'config' . DS);

/**
 * File path to the webroot directory.
 */
define('WWW_ROOT', ROOT . DS . 'webroot' . DS);

/**
 * Path to the tests directory.
 */
define('TESTS', ROOT . DS . 'tests' . DS);

/**
 * Path to the temporary files directory.
 */
define('TMP', ROOT . DS . 'tmp' . DS);

/**
 * Path to the logs directory.
 */
define('LOGS', ROOT . DS . 'logs' . DS);

/**
 * Path to the cache files directory. It can be shared between hosts in a multi-server setup.
 */
define('CACHE', TMP . 'cache' . DS);

/**
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 *
 * CakePHP should always be installed with composer, so look there.
 */
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');

/**
 * Path to the cake directory.
 */
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

define('VOUCHER_501', '501.00');
define('VOUCHER_100', '100.00');
define('SCENT_SHOT_ID', '7');
define('REFILL_ID', '4');

define('PRIVE_PRODUCT_ID', '558'); // Prive ID
define('PRIVE_DISCOUNT', '10'); // Prive Discounts in percentage
define('PRIVE_POINTS', '10'); // Prive Points in percentage


define('REGISTER_VOUCHER_AMOUNT', '0'); //501
define('REGISTER_PB_POINTS', '0'); //500
define('REGISTER_PB_CASH', '0'); //50

define('VALID_VOUCHER_PRODUCT', '1000.00');
define('PB_POINTS_DISCOUNT_1', '5');
define('PB_POINTS_DISCOUNT_2', '10');
define('PB_POINTS_DISCOUNT_3', '15');
define('PB_POINTS_DISCOUNT_4', '20');
define('PB_POINTS_REUTRN', '10');
define('PB_CASH_REUTRN', '5');
define('COD_AMOUNT', '20');
define('REFER_AND_EARN', ['0'=>'A','1'=>'C','2'=>'H','3'=>'B','4'=>'F','5'=>'G','6'=>'X','7'=>'S','8'=>'P','9'=>'N']);
define('PB_BANNER_STATUS', 1);
$comm = [
    'EMAILV' => [
        'timeout'=>'10',
        'token' => 'tE69iSBRlGMdgzkR5rKniND6n', 
        'base_url' => 'https://api.millionverifier.com/api/v3/',
    ]
];
define('SYS', $comm);
$PC = [
    'PRIVE_PRODUCT_ID' => 0,
    'POINTS_DISCOUNT_1' => 0,
    'POINTS_DISCOUNT_2' => 0,
    'POINTS_DISCOUNT_3' => 0,
    'POINTS_DISCOUNT_4' => 0,
    'POINTS_REUTRN' => 0,
    'COD_AMOUNT' => 20,
    'BANNER_STATUS' => 0,
    'OTP' => 111111,
    'SELLER_GST' => '07ABDCS3913P1ZY',
    'USERID' => 115760,
    'IMAGE' => 'https://storage.googleapis.com/perfumersclub/images/PC1000011/EternalLove-380Pixel-1.png',
    'OOS_IMAGE' =>'https://www.perfumersclub.com/pb/subscription_api/img/is-base.jpg',
    'ORDER_PREFIX' =>'PC',
    'REFER_TIME' => 30*24*60*60, //valid upto 30 Days
    'RAZORPAY' => ['keyId' => 'rzp_live_wErilGyAY8Ktpg', 'secretKey' => 'lpyW8jLF5SMZN75MZBtZ4KGa'],
    'PAYTM' => [
        'PAYTM_MERCHANT_KEY' => '!Oo!6nTvHypefS3Q', 
        'PAYTM_MERCHANT_MID' => 'WoGZEI95460886699669',
        'PAYTM_MERCHANT_WEBSITE' => 'DEFAULT',
        'INDUSTRY_TYPE_ID' => 'Retail',
        'PAYTM_STATUS_QUERY_NEW_URL' => 'https://securegw.paytm.in/order/status',
        'PAYTM_TXN_URL' => 'https://securegw.paytm.in/order/process'
    ],
    'MOBIK' => ['merchantId' => '889f35a1bce64f44997304949a893eea', 'secretKey' => '2bb86460ecd74129a08fd08822efa13d', 'txnPostUrl'=>'https://api.zaakpay.com/api/paymentTransact/V7'],
    'PAYPAL' => ['clientId'=>'ASlMr4i134WNPp8ze1WIFzFAEbsztR7tMW3IMEY-Bk08ilZAh1d9TKf_eqz5vJfmk7wwqoZoqcksE_UW','secretKey'=>'EDnodH26YlQ5kZ5PLOhKMkRQDysSrPKTiRWg41s5u4mI-d8jEJNT_-vczpW-N3f8E5b2Sq4QnCJ2ZMjg'],
    'DLYVERY' => [
        'token' => 'f3b2249c0fb50eb5d2b6f2f25780fffd9a8b8447', 
        'api' => 'https://track.delhivery.com/c/api/',
        'api_base_url' => 'https://track.delhivery.com/',
        'client_name' => 'SANGRILA SURFACE',
        'pickup_location' => 'SANGRILA SURFACE',
        'user_name' => 'SANGRILASURFACE',
        'track_package' => 'https://www.delhivery.com/track/package/'
    ],
    'SROKET' => [
        'token' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjExNzMxMSwiaXNzIjoiaHR0cHM6XC9cL2FwaXYyLnNoaXByb2NrZXQuaW5cL3YxXC9leHRlcm5hbFwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NTc3MzAyNDcsImV4cCI6MTU1ODU5NDI0NywibmJmIjoxNTU3NzMwMjQ3LCJqdGkiOiJhY2ZjZmIwMWNiNjk2YjYwMWFhNjRhNzJjYThkMTljZCJ9.xQyy5rn3TwPFm0dnI_Dr5XvCv5yp8L1UGfTRMO1KWgk', 
        'base_url' => 'https://apiv2.shiprocket.in/v1/external/',
        'channel_id' => 160468,
        'client_name' => 'PERFUMEBOOTH',
        'pickup_location' => 'PERFUMEBOOTH'
    ],
    'COMPANY' => [
        'name' =>'Sangrila Lifestyle Pvt Ltd',
        'title' =>'Buy Online Perfume Fragrance | Perfume Selfie For Men and Women',
        'tag' =>"Perfumer's Club",
        'add' =>'70B/35A, 3rd Floor, Rama Road Industrial Area',
        'city' =>'New Delhi',
        'state' =>'Delhi',
        'country' =>'India',
        'pin' =>'110015',
        'phone' =>'9811830806',
        'email' =>'connect@perfumersclub.com',
        'website' =>'https://www.perfumersclub.com',
        'start_year' => '2020',
        'facebook' =>'https://www.facebook.com/perfumersclub/',
        'youtube' =>'https://www.youtube.com/channel/UCLh3vMMFgD_iy3Q5tI5kKeg',
        'twitter' =>'https://twitter.com/PerfumersClub',
        'pinterest' =>'https://in.pinterest.com/perfumersclub/',
        'instagram' =>'https://www.instagram.com/perfumers_club/',
        'homeyoutube' =>'https://www.youtube.com/channel/UCLh3vMMFgD_iy3Q5tI5kKeg',
        'strip' => '20% CashBack <small>(All Orders)</small>'
    ],
    'SENDGRID' =>[
        'token' => 'Bearer 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',
        'base_url' => 'https://api.sendgrid.com/v3/',
        'cat'=>'PC-',
        'list'=> 2000
    ],
    'SMS' =>[
        'key' => 'Aa955fed090eb72361610577594051069',
        'base_url' => 'http://api-alerts.solutionsinfini.com/v4/',
        'sender_id' => 'PRCLUB'
    ],
    'EMAIL_HOST'=>['www.perfumebooth.com', 'www.perfumersclub.com'],
    'TEST_EMAIL' => 'mohd.afroj@perfumebooth.com'
];
define('PC', $PC);
