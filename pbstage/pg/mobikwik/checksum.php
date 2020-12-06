<?php

class Checksum
{
    public static $merchantId = '051debedb12248f1ae4554ecddf1b853'; //for live
    public static $secretKey = 'c556f121672e4abb9dea06fc3d045e68'; //for live
    public static $txnPostUrl = 'https://api.zaakpay.com/api/paymentTransact/V8'; //for live
    //public static $merchantId = 'b19e8f103bce406cbd3476431b6b7973'; //for testing
    //public static $secretKey  = '0678056d96914a8583fb518caf42828a'; //for testing
    //public static $txnPostUrl = 'http://zaakpaystaging.centralindia.cloudapp.azure.com:8080/api/paymentTransact/V8'; //for testing
    public static $mode = '0';
    public static $showMobile = 'true';
    public static $currency = 'INR';

    public static function calculateChecksum($secret_key, $all)
    {
        $hash = hash_hmac('sha256', $all, $secret_key);
        $checksum = $hash;
        return $checksum;
    }

    public static function getAllParams()
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
    public static function getAllParamsCheckandUpdate()
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
    public static function outputForm($checksum)
    {
        //ksort($_POST);
        foreach ($_POST as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />' . "\n";
        }
        echo '<input type="hidden" name="checksum" value="' . $checksum . '" />' . "\n";
    }

    public static function verifyChecksum($checksum, $all, $secret)
    {
        $cal_checksum = Checksum::calculateChecksum($secret, $all);
        $bool = 0;
        if ($checksum == $cal_checksum) {
            $bool = 1;
        }

        return $bool;
    }

    public static function outputResponse($bool)
    {
        foreach ($_POST as $key => $value) {
            if ($bool == 0) {
                if ($key == "responseCode") {
                    echo '<tr><td width="50%" align="center" valign="middle">' . $key . '</td>
						<td width="50%" align="center" valign="middle"><font color=Red>***</font></td></tr>';
                } else if ($key == "responseDescription") {
                    echo '<tr><td width="50%" align="center" valign="middle">' . $key . '</td>
						<td width="50%" align="center" valign="middle"><font color=Red>This response is compromised.</font></td></tr>';
                } else {
                    echo '<tr><td width="50%" align="center" valign="middle">' . $key . '</td>
						<td width="50%" align="center" valign="middle">' . $value . '</td></tr>';
                }
            } else {
                echo '<tr><td width="50%" align="center" valign="middle">' . $key . '</td>
					<td width="50%" align="center" valign="middle">' . $value . '</td></tr>';
            }
        }
        echo '<tr><td width="50%" align="center" valign="middle">Checksum Verified?</td>';
        if ($bool == 1) {
            echo '<td width="50%" align="center" valign="middle">Yes</td></tr>';
        } else {
            echo '<td width="50%" align="center" valign="middle"><font color=Red>No</font></td></tr>';
        }
    }
    public static function getAllResponseParams()
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

    //custom functions
    public static function pgResEnDe($string, $action = 'e')
    {
        // you may change these values to your own
        $secret_key = 'pb';
        $secret_iv = 'google';

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'e') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } else if ($action == 'd') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    public static function getBasePath()
    {
        $basePath = ($_SERVER["HTTPS"] != "on") ? "http://" : "https://";
        $basePath .= $_SERVER["SERVER_NAME"];
        if (strpos($_SERVER["REQUEST_URI"], 'new/') != false) {
            $basePath .= '/new';
        }
        return $basePath;
    }

}
