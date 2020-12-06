<?php
/*

- Use PAYTM_ENVIRONMENT as 'PROD' if you wanted to do transaction in production environment else 'TEST' for doing transaction in testing environment.
- Change the value of PAYTM_MERCHANT_KEY constant with details received from Paytm.
- Change the value of PAYTM_MERCHANT_MID constant with details received from Paytm.
- Change the value of PAYTM_MERCHANT_WEBSITE constant with details received from Paytm.
- Above details will be different for testing and production environment.

 */
define('PAYTM_ENVIRONMENT', 'PROD'); // PROD/TEST
$PAYTM_DOMAIN = "pguat.paytm.com";

if (PAYTM_ENVIRONMENT == 'PROD') {
    $PAYTM_DOMAIN = 'secure.paytm.in';
    define('PAYTM_MERCHANT_KEY', 'g2YI48FfmHVggX4D'); //Change this constant's value with Merchant key downloaded from portal
    define('PAYTM_MERCHANT_MID', 'PerBoo78208550971906'); //Change this constant's value with MID (Merchant ID) received from Paytm
    define('PAYTM_MERCHANT_WEBSITE', 'PerBooWEB'); //Change this constant's value with Website name received from Paytm
    define('INDUSTRY_TYPE_ID', 'Retail109'); //given by paytm
    define('CHANNEL_ID', 'WEB'); //given by paytm
} else {
    define('PAYTM_MERCHANT_KEY', 'KEHaGYfddLsTpjKj'); //Change this constant's value with Merchant key downloaded from portal
    define('PAYTM_MERCHANT_MID', 'Perfum89399363137195'); //Change this constant's value with MID (Merchant ID) received from Paytm
    define('PAYTM_MERCHANT_WEBSITE', 'WEB_STAGING'); //Change this constant's value with Website name received from Paytm
    define('INDUSTRY_TYPE_ID', 'Retail'); //given by paytm
    define('CHANNEL_ID', 'WEB'); //given by paytm
}

define('PAYTM_REFUND_URL', 'https://' . $PAYTM_DOMAIN . '/oltp/HANDLER_INTERNAL/REFUND');
define('PAYTM_STATUS_QUERY_URL', 'https://' . $PAYTM_DOMAIN . '/oltp/HANDLER_INTERNAL/TXNSTATUS');
define('PAYTM_STATUS_QUERY_NEW_URL', 'https://' . $PAYTM_DOMAIN . '/oltp/HANDLER_INTERNAL/getTxnStatus');
define('PAYTM_TXN_URL', 'https://' . $PAYTM_DOMAIN . '/oltp-web/processTransaction');
