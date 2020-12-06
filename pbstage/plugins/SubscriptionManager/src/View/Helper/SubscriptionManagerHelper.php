<?php
namespace SubscriptionManager\View\Helper;
use Cake\View\Helper;

class SubscriptionManagerHelper extends Helper
{
	public $priceLogo = '&#x20B9;';
	public $discountType = [''=>'Select Type','percentage'=>'Percentage','rupees'=>'Rupees','tier1'=>'Tier-1 Price','tier2'=>'Tier-2 Price','tier3'=>'Tier-3 Price'];
	public $selectMenuOptions = ['50'=>'50','100'=>'100','200'=>'200','500'=>'500','700'=>'700','1000'=>'1000','1500'=>'1500','2000'=>'2000','5000'=>'5000','10000'=>'10000','20000'=>'20000'];
	public $siteStatus = [''=>'Select Status','active'=>'Active','inactive'=>'Inactive'];
	public $ruleUsages = [''=>'Select Usages','onetime'=>'Onetime','multipletime'=>'Multipletime'];
	public $siteGender = ['male'=>'Male','female'=>'Female','unisex'=>'Unisex', 'mfemale'=>'Male-Female'];
    public $paymentGateway = ['paytm'=>'PayTm', 'mobikwik'=>'MobiKwik', 'paypal' =>'PayPal', 'razorpay'=>'RazorPay', 'cod'=>'Cash On Delivery'];
    public $planDuration = ['1' =>'Yearly', '2'=>'Half Yearly', '3'=>'Quaterly', '4'=>'Monthly'];
	
	public $userStatus = ['active'=>'Active', 'block'=>'Block','inactive'=>'Inactive'];
	public $reviewStatus = ['approved'=>'Approved', 'pending'=>'Pending','not_approved'=>'Not approved'];
	public $walletStatus = ['failure'=>'Failure','success'=>'Success'];
	
	public $customerStatus = ['active'=>'Active','block'=>'Block','inactive'=>'Inactive'];
	public $customerGroup = ['general'=>'General','retailer'=>'Retailer','wholeseller'=>'Wholeseller'];
	
	public $productTax = ['gst_28_%'=>'28% GST','gst_18_%'=>'18% GST','gst_12_%'=>'12% GST','gst_05_%'=>'05% GST'];
	public $productStatus = ['in_stock'=>'In Stock', 'out_of_stock'=>'Out of Stock', 'coming_soon'=>'Coming Soon'];
	public $productType = ['alcholic'=>'Alcholic', 'non_alcholic'=>'Non Alcholic'];
	public $productPerfumeType = ['attar'=>'Attar', 'edp'=>'EDP', 'edt'=>'EDT', 'edit'=>'EDIT', 'pdt'=>'PDT'];
	public $productNote = ['top_note'=>'Top Note','middle_note'=>'Middle Note','base_note'=>'Base Note'];
	public $productSize = ['ml'=>'ML','gms'=>'GMS'];
	public $productTags = [''=>'Select Tag', 'Best Seller'=>'Best Seller','Money'=>'Money','New'=>'New','Premium'=>'Premium','Trending'=>'Trending'];
	public $productDots = [''=>'Select Dots Colour', 'Challenger_color'=>'Challenger','Dreamer_color'=>'Dreamer','EternalLove_color'=>'Eternal Love','Gentalmen_color'=>'Gental Men','IntoTheWild_color'=>'Into The Wild','NightQueen_color'=>'Night Queen','PartyAnimal_color'=>'Party Animal','Swag_color'=>'Swag','Trendsetter_color'=>'Trendsetter','Wanderer_color'=>'Wanderer','WildChild_color'=>'Wild Child'];
	
	public $buckets 		= [['id'=>1, 'title'=>'All'],['id'=>2, 'title'=>'Abandoned Cart'],['id'=>3, 'title'=>'Post Purchase'],['id'=>4, 'title'=>'Registered']];
	public $driftMailer 	= [''=>'Select Type','login'=>'Login Page','cart'=>'Cart Page','checkout'=>'Checkout Page'];
	public $mailerSchedule 	= ['Manually', 'Automatically (Days)','Automatically (Hours)'];

	public $mailerConditions	= [
		'delivered'=>'Purchased',
		'repeated'=>'Repeated Purchased',
		'cart'=>'Abandoned Cart',
		'perfume'=>'Perfume Buyers',
		'deo'=>'Deo Buyer',
		'scent_shot'=>'Scent Shot Buyers',
		'refill'=>'Refill Buyers',
		'member'=>'Prive Members',
		'never'=>'Not Purchased'
	];

	public $orderStatus = [
		'cancelled'					=>'Cancelled',
		'cancelled_by_customer'		=>'Cancelled By Customer',
		'pending'					=>'Pending',
		'accepted'					=>'Accepted',
		'proccessing'				=>'Processing',
		'dispatched'				=>'Dispatched',
		'intransit'					=>'Intransit',
		'delivered'					=>'Delivered',
		'refund'					=>'Refund',
		'rto'						=>'RTO',
		'dto'						=>'DTO',
		'lost'						=>'Lost',
		'paymentfail'				=>'Payment Fail'
	];

	public $courierCompany = [
		'0'  =>'Select Courier',
		'1'  =>'Bluedart',
		'2'  =>'Fedex',
		'3'  =>'PB Delhivery',
		'7'  =>'Fedex Packaging*',
		'8'  =>'DHL Packet International*',
		'10'  =>'Delhivery',
		'12'  =>'Fedex Surface',
		'14'  =>'Ecom Exp',
		'16'  =>'Dotzot',
		'33'  =>'Xpressbees',
		'35'  =>'Aramex International*',
		'36'  =>'Fedex Your Pack*',
		'37'  =>'DHL Packet Plus*',
		'38'  =>'DHL Parcel Direct*',
		'39'  =>'Delhivery Surface',
		'40'  =>'Gati Surface',
		'41'  =>'Fedex FR',
		'42'  =>'Fedex SL',
		'43'  =>'Delhivery Surface Standard',
		'44'  =>'Delhivery Surface Lite',
		'45'  =>'EcomExp Reserve**',
		'46'  =>'Shadow Fax Reverse**',
		'47'  =>'Ecom International*'
	];
	
	/*Return Z/A if date are empty or unix system define*/
	public function emptyDate($param){
		return ($param == "") ? 'N/A': date('Y-m-d h:i:s A', strtotime($param));
	}
	
	/*Return N/A if $param are empty*/
	public function checkValue($param){
		return ($param == "") ? 'N/A': $param;
	}
}