<?php
namespace SubscriptionApi\Controller\Component;

use Cake\Controller\Component;

class MobikwikComponent extends Component
{
    protected $merchantId = '889f35a1bce64f44997304949a893eea'; //'b19e8f103bce406cbd3476431b6b7973'; //for testing
    protected $secretKey  = '2bb86460ecd74129a08fd08822efa13d'; //''0678056d96914a8583fb518caf42828a'; //for testing
    protected $txnPostUrl = 'https://api.zaakpay.com/api/paymentTransact/V7'; //'http://zaakpaystaging.centralindia.cloudapp.azure.com:8080/api/paymentTransact/V8'; //for testing
    protected $mode = '1';
    protected $showMobile = 'true';
    protected $currency = 'INR';
    public function __construct(){
        // Change for live account
        if( $this->mode ) {
            $this->merchantId = PC['MOBIK']['merchantId'];
            $this->secretKey = PC['MOBIK']['secretKey'];
            $this->txnPostUrl = PC['MOBIK']['txnPostUrl'];
        }
    }
    public function calculateChecksum($secret_key, $all)
    {
        $hash = hash_hmac('sha256', $all, $secret_key);
        $checksum = $hash;
        return $checksum;
    }

    public function getAllParams()
    {
        //ksort($_POST);
        $all = '';

        $checksumsequence = array("amount", "bankid", "buyerAddress",
            "buyerCity", "buyerCountry", "buyerEmail", "buyerFirstName", "buyerLastName", "buyerPhoneNumber", "buyerPincode",
            "buyerState", "currency", "debitorcredit", "merchantIdentifier", "merchantIpAddress", "mode", "orderId",
            "product1Description", "product2Description", "product3Description", "product4Description",
            "productDescription", "productInfo", "purpose", "returnUrl", "shipToAddress", "shipToCity", "shipToCountry",
            "shipToFirstname", "shipToLastname", "shipToPhoneNumber", "shipToPincode", "shipToState", "showMobile", "txnDate",
            "txnType", "zpPayOption");

        foreach ($checksumsequence as $seqvalue) {
            if (array_key_exists($seqvalue, $_POST)) {
                if ((!$_POST[$seqvalue] == "") || ($seqvalue == 'mode')) {
                    if ($seqvalue != 'checksum') {
                        $all .= $seqvalue;
                        $all .= "=";
                        $all .= $_POST[$seqvalue];
                        $all .= "&";
                    }
                }

            }
        }
        return $all;
    }
    public function getAllParamsCheckandUpdate()
    {
        //ksort($_POST);
        $all = '';
        foreach ($_POST as $key => $value) {
            if ($key != 'checksum') {
                $all .= "'";
                $all .= $value;
                $all .= "'";
            }
        }

        return $all;
    }
    public function outputForm($checksum)
    {
        $res = '';
        //ksort($_POST);
        foreach ($_POST as $key => $value) {
            $res .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />' . "\n";
        }
        $res .= '<input type="hidden" name="checksum" value="' . $checksum . '" />' . "\n";
        return $res;
    }

    public function verifyChecksum($checksum, $all, $secret)
    {
        $cal_checksum = $this->calculateChecksum($secret, $all);
        $bool = 0;
        if ($checksum == $cal_checksum) {
            $bool = 1;
        }

        return $bool;
    }

    public function outputResponse($bool)
    {
        $res = '';
        foreach ($_POST as $key => $value) {
            if ($bool == 0) {
                if ($key == "responseCode") {
                    $res .= '<tr><td width="50%" align="center" valign="middle">' . $key . '</td>
						<td width="50%" align="center" valign="middle"><font color=Red>***</font></td></tr>';
                } else if ($key == "responseDescription") {
                    $res .= '<tr><td width="50%" align="center" valign="middle">' . $key . '</td>
						<td width="50%" align="center" valign="middle"><font color=Red>This response is compromised.</font></td></tr>';
                } else {
                    $res .= '<tr><td width="50%" align="center" valign="middle">' . $key . '</td>
						<td width="50%" align="center" valign="middle">' . $value . '</td></tr>';
                }
            } else {
                $res .= '<tr><td width="50%" align="center" valign="middle">' . $key . '</td>
					<td width="50%" align="center" valign="middle">' . $value . '</td></tr>';
            }
        }
        $res .= '<tr><td width="50%" align="center" valign="middle">Checksum Verified?</td>';
        if ($bool == 1) {
            $res .= '<td width="50%" align="center" valign="middle">Yes</td></tr>';
        } else {
            $res .= '<td width="50%" align="center" valign="middle"><font color=Red>No</font></td></tr>';
        }
        return $res;
    }
    public function getAllResponseParams()
    {
        //ksort($_POST);
        $all = '';
        $checksumsequence = array("amount", "bank", "bankid", "cardId",
            "cardScheme", "cardToken", "cardhashid", "doRedirect", "orderId",
            "paymentMethod", "paymentMode", "responseCode", "responseDescription",
            "productDescription", "product1Description", "product2Description",
            "product3Description", "product4Description", "pgTransId", "pgTransTime");
        foreach ($checksumsequence as $seqvalue) {
            if (array_key_exists($seqvalue, $_POST)) {

                $all .= $seqvalue;
                $all .= "=";
                $all .= $_POST[$seqvalue];
                $all .= "&";
            }
        }
        return $all;
    }
}
