<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

class PayuComponent extends Component
{
    private $prodMode = true;
    protected $payu_key = 'gtKFFx'; ////Change this constant's value with Merchant key downloaded from Payu
    protected $payu_salt = 'eCwWELxi'; ////Change this constant's value with salt Merchant received from Payu
    protected $payu_base_url = 'https://test.payu.in/_payment';
    public function __construct()
    {
        if ($this->prodMode) {
            $this->payu_key = 'kl6JhP';
            $this->payu_salt = 'gY1lxrD3';
            $this->payu_base_url = 'https://secure.payu.in/_payment';
        }
    }

    public function createHash($postdata)
    {
        $txnid = $postdata['txnid'] ?? 0;
        $amount = $postdata['amount'] ?? 0;
        $productinfo = $postdata['productinfo'] ?? ''; //pinfo
        $firstname = $postdata['firstname'] ?? '';
        $email = $postdata['email'] ?? '';
        $udf5 = $postdata['udf5'] ?? '';
        return $hash = hash('sha512', $this->payu_key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email . '|||||' . $udf5 . '||||||' . $this->payu_salt);
    }

    public function verifyHash($postdata)
    {
        $bool = 0;
        $txnid = $postdata['txnid'];
        $amount = $postdata['amount'];
        $productInfo = $postdata['productinfo'];
        $firstname = $postdata['firstname'];
        $email = $postdata['email'];
        $udf5 = $postdata['udf5'];
        $status = $postdata['status'];
        $resphash = $postdata['hash'];
        //Calculate response hash to verify
        $keyString = $this->payu_key . '|' . $txnid . '|' . $amount . '|' . $productInfo . '|' . $firstname . '|' . $email . '|||||' . $udf5 . '|||||';
        $keyArray = explode("|", $keyString);
        $reverseKeyArray = array_reverse($keyArray);
        $reverseKeyString = implode("|", $reverseKeyArray);
        $CalcHashString = strtolower(hash('sha512', $this->payu_salt . '|' . $status . '|' . $reverseKeyString));
        if ($resphash == $CalcHashString) {
            $bool = 1;
        }
        return $bool;
    }
}
