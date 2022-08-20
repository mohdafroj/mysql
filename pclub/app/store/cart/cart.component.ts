import { Component, OnInit, DoCheck, ViewChild, ElementRef } from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { ToastrService } from 'ngx-toastr';

import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { TrackingService }	from './../../_services/tracking.service';

@Component({
  selector: '[pc-cart]',
  templateUrl: './cart.component.html',
  styleUrls: [
		'./cart.component.css'
	]
})
export class CartComponent implements OnInit, DoCheck {
	@ViewChild('hideDeleteModal', {static: false}) hideDeleteModal: ElementRef;
	shoppingCart = []; //GETFOR399
	shoppingPack = [];
	cartCurrency;
	cartTotal = 0.00;
	couponForm:FormGroup;
	customerInfo;	  
	accMsg	= '';
	userId	= 0;	
	buster;
	busterProducts = [];
	busterGender = 'male';	
	couponCode	= '';
	couponMsg = '';
	confimMsg:string						='';
	removeItem = {};
	inputData:any							=[];
	optionStatus:number 					= 1;
	loaderStatus = 0;
	//Start Login/Register detail
	serverRequest = false;
	userTypeForm:FormGroup;
	loginForm:FormGroup;
	registerForm:FormGroup;
	loginStatusType = 'newuser';
	tokenForAccount = '';
	myFormData: any;
	resObj;
	isEmail = 0;
	isStep = 1;
	oldUsername;
	oldEmail;
	oldMobile;
	oldOtp;
	//customer address
	addressForm:FormGroup; 
	addresses = [];
	selectedAddress = {};
	initAddress	= {};
	addressId	= -1;
	addressResponse	= {};
	pincodeStatus = 0;
	methodForm:FormGroup;
	paymentMethodData = [];
	finalStatus:boolean	= false;
	finalMessage = '';
	deliveryMessage = '';
	shippingAmount = 0;
	totalAmountAfterDiscount = 0;
	credits	= [];
	cartCouponList	= [];
	discounts;
	codAmount = 0;
	grandTotal = 0;
	grandFinalTotal = 0;
	otpResponse = {};
	orderPlaceStatus = 0;
	paymentGatewayUrl = '';
	customerAuth = '';
	orderId = 0;
	sanitizer:any;
	constructor 
	(
		private router: Router,
		private route: ActivatedRoute,
		private config: Myconfig, 
		private elem: ElementRef, 
		private customer: CustomerService, 
		private store: StoreService, 
		private track: TrackingService, 
		private sanitize: DomSanitizer,
		private toastr: ToastrService
	)
	{
		this.sanitizer = sanitize;
		this.initAddress = {
			id:0,
			firstname: customer.getFirstName(),
			lastname: customer.getLastName(),
			address: customer.getAddress(),
			city: customer.getCity(),
			state: '',
			country: 'India',
			pincode: customer.getPincode(),
			email:customer.getEmail(),
			mobile:customer.getMobile(),
			set_default:0
		};
	}

	ngOnInit() {
		this.buster = {products:[], price:0, label:''};
		this.cartCurrency = this.config.cartCurrency;
		this.customerAuth = this.customer.getToken();
		this.config.scrollToTop();
		this.userId = this.customer.getId();
		this.couponForm = new FormGroup ({
			inCouponCode: new FormControl("")
		});
	    this.paymentGatewayUrl = this.config.paymentGatewayUrl;
		this.resObj = {message:'', textClass:'', otpMessage:'', otpClass:''};
		this.userTypeForm = new FormGroup ({ loginStatusType: new FormControl(this.loginStatusType, Validators.required) });
		this.loginForm = new FormGroup ({
			username: new FormControl("", this.loginUsernameValidator),
			otp: new FormControl("")
		});
		this.registerForm = new FormGroup ({
			username: new FormControl("", Validators.compose([Validators.required]) ),
			email: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
			mobile: new FormControl("", Validators.compose([Validators.required, Validators.minLength(10)]) ),
			otp: new FormControl("", Validators.compose([Validators.minLength(6)]) )
		});
		this.initAddressForm(this.initAddress);
		this.getAddresses(); //this.inputData.paymentMethod = 1;
		this.inputData = this.store.getCartInfo();
		
		this.optionStatus = this.inputData['optionStatus'] ? this.inputData['optionStatus'] : 1;
		this.methodForm = new FormGroup ({
			paymentMethod: new FormControl(this.inputData['paymentMethod'], Validators.required)
		});
		this.customerInfo = {cash:0, voucher: 0, points: 0};
		this.discounts = {cash:0, voucher: 0, points: 0, amount:0, coupon:0, extra:0};
		this.getMyCart();
	}

	loginUsernameValidator (control) {
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		let MOBILE_REGEXP = /^[1-9]{1}[0-9]{9}$/;
		if( !EMAIL_REGEXP.test(control.value) ) {
			if (!MOBILE_REGEXP.test(control.value)) {
				return {'username': true};
			}
		}
	}

	customerLogin(formData){
		this.myFormData		= formData;
		let formAction= 1;
		if ( this.loginForm.controls.username.invalid || (formData.username == '') ){
			this.loginForm.controls.username.markAsDirty();
			if ( this.isEmail == 1 ) {
				this.resObj.message = 'Please enter valid email id!';
			} else {
				this.resObj.message = 'Please enter valid mobile number!';
			}
			this.resObj['textClass'] = 'text-danger';
			formAction = 0;
		}
		if( (this.isStep == 2) && (formData.username != "") ){
			if( formData.otp == '' ){
				formAction	= 0;
				this.resObj.otpMessage = 'Please enter valid OTP!';
				this.resObj.otpClass = 'text-danger';
			}
		}
		if( formAction ){
			if(this.isStep == 2){
				this.resObj.otpMessage = 'Wait...';
				this.resObj.otpClass = 'text-warning';
			}else{
				this.resObj.message = 'Wait...';
				this.resObj.textClass = 'text-warning';
			}
			if ( this.serverRequest == false ) {
				this.serverRequest = true;
				formData.isEmail = this.isEmail;
				formData.isStep = this.isStep;
				let myCart = this.customer.getFromCart();
				let carItemIds = myCart['shopping']['cart'].map( (item) => { return item.id; });
				let carItemQuantities = myCart['shopping']['cart'].map( (item) => { return item.cart_quantity; });
				formData.productId = carItemIds.join(',');
				formData.quantity = carItemQuantities.join(',');				
				this.customer.signIn(formData).subscribe(
					(res)=> {
						this.serverRequest = false;
						if(res.status){
							if(this.isStep == 2){
								this.customer.setAccount(res.data);
								this.ngOnInit();
							}else{
								let str = ( this.isEmail == 1 ) ? 'email id':'mobile number';
								this.resObj.message = 'We have sent OTP on entered '+str+' "'+formData.username+'"';
								this.resObj.textClass = '';
								this.isStep = 2;
								formAction	= 0;
							}
						} else {
							if(this.isStep == 2){
								this.resObj.otpMessage = res.message;
								this.resObj.otpClass = 'text-danger';
							}else{
								this.resObj.textClass = 'text-danger';
								this.resObj.message = res.message;
							}
						}
					},
					(err: HttpErrorResponse) => {
						this.serverRequest = false;
						this.resObj.otpClass = 'text-danger resend_otp';
						if(err.error instanceof Error){
							this.resObj.message = 'Client error: '+err.error.message;
						}else{
							this.resObj.message = 'Server error: There are some server issue.';
						}
					}
				);
			} else {
				this.toastr.warning("Please wait ...");
			}
		} else {
			this.resObj.textClass = 'text-danger';
		}
	}

	getBack() {
		this.isStep = 1;
		this.oldUsername = this.oldEmail = this.oldMobile = this.oldOtp = '';
		this.resObj = {message:'', textClass:'', otpMessage:'', otpClass:''};
	}

	resendLoginOtp(){
		this.resObj.otpMessage = 'Wait...';
		this.resObj.otpClass = 'text-warning';
		this.myFormData.isStep = 1;
		this.myFormData.otp = '';
		this.loginForm.controls.otp.setValue('', {});
		this.customer.signIn(this.myFormData).subscribe(
			(res)=> {
				if(res.status){
					this.resObj.otpMessage = '';
					this.resObj.textClass = '';
					this.isStep = 2;
				}else{
					this.resObj.textClass = 'text-danger';
					this.resObj.message = res.message;
				}
			},
			(err: HttpErrorResponse) => {
				this.resObj.otpClass = 'text-danger resend_otp';
				if(err.error instanceof Error){
					this.resObj.message = 'Client error: '+err.error.message;
				}else{
					this.resObj.message = 'Server error: '+JSON.stringify(err.error);
				}
			}
		);
	}

	customerRegister( formData ) { 
		formData.isStep = this.isStep;
		this.myFormData	= formData;
		let formAction = 1;
		if( this.registerForm.controls.username.invalid || (formData.username == '') ){
			this.registerForm.controls.username.markAsDirty();
			this.resObj['message'] = 'Please enter user name!';
			this.resObj['textClass'] = 'text-danger';
			formAction	= 0;
		}
		if( this.registerForm.controls.mobile.invalid || !this.config.MOBILE_REGEXP.test(formData.mobile) ){
			this.registerForm.controls.mobile.markAsDirty();
			this.resObj['message'] = 'Please enter 10 digits mobile number!';
			this.resObj['textClass'] = 'text-danger';
			formAction	= 0;
		}
		if( this.registerForm.controls.email.invalid || (formData.email == "") ){
			this.resObj['message'] = 'Please enter a valid email id!';
			this.resObj['textClass'] = 'text-danger';
			this.registerForm.controls.email.markAsDirty();
			formAction	= 0;
		}
		if( (formData.username == '') || (formData.username == '') || (formData.email == "") ){
			this.resObj['message'] = 'Please fill all fields!';
			this.resObj['textClass'] = 'text-danger';
			formAction	= 0;
		}
		if( (this.isStep == 2) && (formData.username != "") ){
			if( formData['otp'] == "" ){
				formAction	= 0;
				this.resObj['otpMessage'] = 'Please enter 6 digits OTP!';
				this.resObj['otpClass'] = 'text-danger';
			}else{
				formData.token = this.tokenForAccount;
			}
		}
		
		if( formAction ){
			if ( this.isStep == 2 ){
				this.resObj['otpMessage'] = 'Wait...';
				this.resObj['otpClass'] = 'text-warning';
			} else {
				this.resObj['message'] = 'Wait...';
				this.resObj['textClass'] = 'text-warning';
			}
			this.route.queryParams.subscribe((params: Params) => {
				formData.refer = params['refer'];
			});
			
			if( this.serverRequest == false ){
				this.serverRequest = true;
				let myCart = this.customer.getFromCart();
				let carItemIds = myCart['shopping']['cart'].map( (item) => { return item.id; });
				let carItemQuantities = myCart['shopping']['cart'].map( (item) => { return item.cart_quantity; });
				formData.productId = carItemIds.join(',');
				formData.quantity = carItemQuantities.join(',');				
				this.customer.signUp(formData).subscribe(
					res => {
						this.serverRequest = false;
						if ( res.status ) {
							if ( this.isStep == 2 ) {
								this.customer.setAccount(res.data);
								this.ngOnInit();
							} else {
								this.resObj['message'] = 'We have sent OTP on mobile number "'+formData.mobile+'" and email id "'+formData.email+'"';
								this.resObj['textClass'] = '';
								this.isStep = 2;
								this.tokenForAccount = res.data.token;
								formAction	= 0;
							}
						} else {
							if ( this.isStep == 2 ) {
								this.resObj['otpMessage'] = res.message;
								this.resObj['otpClass'] = 'text-danger';
							} else {
								this.resObj['textClass'] = 'text-danger';
								this.resObj['message'] = res.message;
							}
						}
					},
					(err: HttpErrorResponse) => {
						this.serverRequest = false;
						this.resObj['otpClass'] = 'text-danger';
						if(err.error instanceof Error){
							this.resObj['message'] = 'Client error: '+err.error.message;
						}else{
							this.resObj['message'] = 'Server error: There are some server issue!';
						}
					}
				);
			}			
		}
		return true;
	}
	
	resendOtpRegister () {
		this.resObj['otpMessage'] = 'Wait...';
		this.resObj['otpClass'] = 'text-warning';
		this.myFormData.isStep = 1;
		this.myFormData.otp = '';
		this.registerForm.controls.otp.setValue('', {});
		if( this.serverRequest == false ){
			this.serverRequest = true;
			this.customer.signUp(this.myFormData).subscribe(
				(res)=> {
					this.serverRequest = false;
					if(res.status){
						this.resObj['otpMessage'] = '';
						this.resObj['textClass'] = '';
						this.isStep = 2;
						this.tokenForAccount = res.data.token;
					}else{
						this.resObj['textClass'] = 'text-danger';
						this.resObj['message'] = res.message;
					}
				},
				(err: HttpErrorResponse) => {
					this.serverRequest = false;
					this.resObj['otpClass'] = 'text-danger';
					if(err.error instanceof Error){
						this.resObj['message'] = 'Client error: '+err.error.message;
					}else{
						this.resObj['message'] = 'Server error: There are some server issue!';
					}
				}
			);
		}
	}

	ngDoCheck() {
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		let MOBILE_REGEXP = /^[1-9]{1}[0-9]{9}$/;
		let loginStatusType = this.userTypeForm.controls.loginStatusType.value;
		let newUsername, newOtp, newEmail;
		if ( loginStatusType == 'olduser' ) {
			newUsername = this.loginForm.controls.username.value;
			newOtp = this.loginForm.controls.otp.value;
			this.loginForm.controls.username.setValue(newUsername.toLowerCase(), {});
			if( EMAIL_REGEXP.test(newUsername) ) {
				this.isEmail = 1;
			}
			if( MOBILE_REGEXP.test(newUsername) ) {
				this.isEmail = 2;
			}
			if (this.oldUsername != newUsername) {
				this.resObj = {message:'', textClass:'', otpMessage:'', otpClass:''};
				this.oldUsername = newUsername;
			}
		} else {
			newUsername = this.registerForm.controls.username.value;
			newEmail = this.registerForm.controls.email.value;
			let newMobile = this.registerForm.controls.mobile.value;
			newOtp = this.registerForm.controls.otp.value;
			this.registerForm.controls.email.setValue(newEmail.toLowerCase(), {});
			if ( (this.oldUsername != newUsername) || (this.oldEmail != newEmail) || (this.oldMobile != newMobile) ) {
				this.oldUsername = newUsername;
				this.oldEmail = newEmail;
				this.oldMobile = newMobile;
				this.resObj = {message:'', textClass:'', otpMessage:'', otpClass:''};
			}
			
		}
		if ( this.oldOtp != newOtp ){
			this.oldOtp = newOtp;
			this.resObj.otpMessage = this.resObj.otpClass = '';
		}
	}

	initAddressForm(value) {
	    this.addressForm = new FormGroup ({
			id			: new FormControl(value.id),
			firstname	: new FormControl(value.firstname, Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP), Validators.minLength(3)]) ),
			lastname	: new FormControl(value.lastname, Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
			address		: new FormControl(value.address, Validators.compose([Validators.required,Validators.minLength(3)]) ),
			city		: new FormControl(value.city, Validators.compose([Validators.required,Validators.minLength(3)]) ),
			state		: new FormControl(value.state, Validators.compose([Validators.required]) ),
			country		: new FormControl(value.country, Validators.required),
			pincode		: new FormControl(value.pincode, Validators.compose([Validators.required]) ), //Validators.pattern(/^\d{6}$/)
			email		: new FormControl(value.email, Validators.compose([Validators.required,Validators.pattern(this.config.EMAIL_REGEXP)]) ),
			mobile		: new FormControl(value.mobile),
			setdefault	: new FormControl(value.id)
		});
		
		let stateValue = '';
		if ( this.addresses['states'] ){
			for( let item of this.addresses['states'] ){
				if( item.title == this.addressForm.value.state ){
					stateValue = item.title;
					break;
				}
			}		
			if( stateValue == '' ) {
				stateValue = this.addresses['states'][0]['title'] ? this.addresses['states'][0]['title'] : '';
			}
		}
		this.addressForm.patchValue({state: stateValue});
	
		let countryValue = '';
		if ( this.addresses['locations'] ){
			for( let item of this.addresses['locations'] ){
				if( item.title == this.addressForm.value.country ){
					countryValue = item.title;
					break;
				}
			}		
			if( countryValue == '' ) {
				countryValue = this.addresses['locations'][0]['title'] ? this.addresses['locations'][0]['title'] : '';
			}
		}
		this.addressForm.patchValue({country: countryValue});
	
	}
	
	getAddresses() {
		this.customer.getAddresses().subscribe(
			res => {
				this.addresses = res.data; //this.addresses['address'] = [];
				if( this.addresses['address'] && this.addresses['address'].length > 0 ){
					for( let item of this.addresses['address'] ){
						if( item.set_default == "1" ){
							this.selectedAddress = item;
							this.addressForm.patchValue({setdefault: item.id});
							//this.elem.nativeElement.querySelector('#RevieworederSelected').click();
							break;
						}
					}
					this.addressId = -1;
				} else {
					this.addressId = 0;
					this.initAddress['firstname'] = this.customer.getFirstName();
					this.initAddress['lastname'] = this.customer.getLastName();
					this.initAddress['address'] = this.customer.getAddress();
					this.initAddress['city'] = this.customer.getCity();
					this.initAddress['email'] = this.customer.getEmail();
					this.initAddress['mobile'] = this.customer.getMobile();
					this.initAddressForm(this.initAddress);
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	
	editAddress(item){
		this.addressId = item.id;
		this.initAddressForm(item);
	}
	
	cancelAddress(){
		this.addressId = -1;
		this.addressResponse['message'] = '';
		this.initAddressForm(this.initAddress);
	}

	addressMessageClear(){
		//this.addressResponse = {};
	}

	addNewAddress(){
		this.addressId = 0;
		this.addressResponse['message'] = '';
		this.initAddressForm(this.initAddress);
	}
	
	fetchAddressByPincode(pincode) {
		this.store.checkPincode(pincode, 1).subscribe(
			res => {
				if ( res.status ) {
					if ( res.data.state != '' ) {
						this.addressForm.patchValue({state:res.data.state});
					}
					if ( res.data.district != '' ) {
						this.addressForm.patchValue({city:res.data.district});
					}
				}
			},
			(err: HttpErrorResponse) => {
			}
		);
	}

	saveAddress(formData){ //console.log(formData, this.serverRequest);
		let checkForm = true;
		if ( formData['firstname'] == '' || formData['lastname'] == '' || formData['address'] == '' || formData['pincode'] == '' || formData['city'] == '' || formData['email'] == '' || formData['mobile'] == '' ) {
			checkForm = false;
		} 
		if( (this.serverRequest == false)  ) { //&& ( checkForm == true )
 			this.addressResponse = { message: '<i class="fa fa-spinner fa-spin"></i>', textClass:'text-danger' };
			this.serverRequest = true;
			this.store.checkPincode(formData.pincode).subscribe(
				res => { //console.log(res);
					this.pincodeStatus = res.status; // 1 both, 2 prepaid, 3 postpaid, 0 not
					this.deliveryMessage = res.message;
					if( this.pincodeStatus > 0 ) {
						formData.setdefault = 1;
						this.customer.addAddresses(formData).subscribe(
							res => {
								this.serverRequest = false;
								if( res.status ){
									this.addresses = res.data;
									if( this.addresses['address'].length > 0 ){
										for( let item of this.addresses['address'] ){
											if( item.set_default == "1" ){
												this.selectedAddress = item;
												break;
											}
										}
									}
									this.initAddressForm(this.initAddress);
									this.addressResponse = {message: '', textClass: 'text-success'};
									this.addressId = -1;
									//this.elem.nativeElement.querySelector('.close').click(); close address popup
									this.getMyCart();
								}else{
									this.addressResponse = { message:res.message, textClass:'text-danger' };
								}
							},
							(err: HttpErrorResponse) => {
								var message = '';
								if(err.error instanceof Error){
									message = 'Client error: '+err.error.message;
								}else{
									message = 'Server error: '+JSON.stringify(err.error);
								}
								this.addressResponse = { message:message, textClass:'text-danger' };
								this.serverRequest = false;
							}
						);
					} else {
						this.addressResponse = { message:res.message, textClass:'text-danger' };
						this.serverRequest = false;
					}
				},
				(err: HttpErrorResponse) => {
					this.addressResponse = { message:'Sorry, may be network issue, please refresh page!', textClass:'text-danger' };
					this.serverRequest = false;
				}
			);
		} else if ( checkForm == false ) {	
			this.addressResponse = { message: 'Please enter !', textClass:'text-danger' };
		} else {
			this.addressResponse = { message:'Please wait...', textClass:'text-danger' };
		}
	}

	getMyCart(){
		let myCart = this.customer.getFromCart();
		this.inputData['shopping'] = myCart['shopping'];		
		this.loaderStatus = 0;
		this.store.getCart(this.inputData).subscribe(
			res => {
				if ( res.status ) {
					if ( res.data['shopping'] ) {
						this.cartCurrency   = res.data.currency;
						this.cartTotal   	= res.data.cart_total;
						this.shoppingCart 	= res.data.shopping.cart;
						this.shoppingPack 	= res.data.shopping.pack;
						this.customerInfo 	= res.data.customer;
						this.discounts 		= res.data.discounts;
						this.credits 		= res.data.credits;
						this.cartCouponList = res.data.cartCoupon;
						this.buster 		= res.data.buster;
						this.busterBuster(this.busterGender);
						this.couponCode 	= res.data.coupon_code;
						this.couponMsg 		= res.data.coupon_msg;
						this.totalAmountAfterDiscount 	= res.data.total_amount_after_discount;
						this.shippingAmount = res.data.shipping_amount;
						this.grandTotal 	= res.data.grand_total_at_cart;
						this.codAmount		= res.data.payment_fees;
						this.grandFinalTotal = res.data.grand_final_total;
	
						this.inputData['shopping'] = res.data.shopping;
						this.paymentMethodData  = res.data.payment_method_data;
						this.methodForm.patchValue({paymentMethod: res.data.payment_method});
					} else {
						this.discounts = {cash:0, voucher: 0, points: 0, amount:0, coupon:0, extra:0};
						this.cartTotal = 0;
						this.totalAmountAfterDiscount 	= 0.00;
						this.shippingAmount 			= 0.00;
						this.grandTotal 				= 0.00;
						this.shoppingCart = [];
						this.cartCouponList = [];
						this.grandFinalTotal = 0;
					}
					this.couponForm.patchValue({inCouponCode:this.inputData.couponCode});
					///this.inputData.couponCode   = this.couponCode;
					
					if( !this.inputData['paymentMethod'] ){
						this.inputData.paymentMethod = res.data.payment_method;
					}
					this.inputData.paymentCode = res.data.payment_method_code;
				} else {
					this.discounts = {cash:0, voucher: 0, points: 0, amount:0, coupon:0, extra:0};
					this.couponCode 				= '';
					this.couponMsg 					= '';
					this.totalAmountAfterDiscount 	= 0.00;
					this.shippingAmount 			= 0.00;
					this.grandTotal 				= 0.00;
					this.cartTotal = 0;
					this.cartCouponList = [];
					this.shoppingCart = myCart['shopping']['cart'];
				}
				this.loaderStatus = 1;
				this.store.setCartInfo(this.inputData);
				this.customer.setCart(this.shoppingCart);
				this.store.setTrackingData(res.data);
				this.track.storeTrack('cart'); //call add to cart page event
				//console.log(this.inputData['paymentCode']);

			},
			(err: HttpErrorResponse) => {
				this.loaderStatus = 1;
				if(err.error instanceof Error){
				  console.log('Client Error: '+err.error.message);
				}else{
				  console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
				}
			}
		);
		
		return true;
	}

	wallet(event: any, num: number) {
		switch (num) {
			case 1: // For pb cash
				this.inputData.cash = event.target.checked; //return status of checkbox
				this.inputData.voucher = false;
				this.inputData.points = false;
				this.optionStatus = num;
				this.inputData.optionStatus = num;
				break;
			case 2: // For vouchar
				this.inputData.cash = false;
				this.inputData.voucher = true;
				this.inputData.points = false;
				this.optionStatus = num;
				this.inputData.optionStatus = num;
				break;
			case 3: // For pb points
				this.inputData.cash = false;
				this.inputData.voucher = false;
				this.inputData.points = true;
				this.optionStatus = num;
				this.inputData.optionStatus = num;
				break;
			case 4: // For coupon code
				//this.inputData.voucher = false;
				//this.inputData.points = false;
				//this.inputData.couponCode = '';
				//this.couponCode = '';
				//this.optionStatus = num;
				//this.inputData.optionStatus = num;
				break;
			case 5: // For prive 99 rupees
				//this.inputData.pbPrive = event.target.checked;
				break;
			default:
		}
        this.getMyCart();
    	return false;
  	}

	addCoupon(coupon){
		//console.log(coupon);
		if( coupon != ""  ){
			this.inputData.couponCode = coupon;
			this.couponMsg	= "Loading...";
			this.getMyCart();
		} else {
			this.couponMsg = 'Please enter coupon code!';
		}
	}
	
	removeCoupon () {
		this.config.setOfferCoupon('');
		this.inputData.couponCode = '';
		this.couponMsg	= "Loading...";
		this.couponForm.patchValue({inCouponCode:''});
		this.getMyCart();
	}
  
	addBoosterProduct (productId) {
		if ( this.userId > 0 ) {
			let formData = {itemId: productId, quantity: 1};
			this.store.addToCart(formData).subscribe(
				res => {
					if ( res.data.cart ) { this.customer.setCart(res.data.cart); }
					if( res.status ){								
						this.toastr.success(res.message);
					} else {
						this.toastr.error(res.message);
					}
					this.getMyCart();
				},
				(err: HttpErrorResponse) => {
					this.toastr.error("Sorry, there are some app issue!");
				}
			);
		}
	}
	
	busterBuster ( gender ) {
		this.busterGender = gender;
		this.busterProducts = this.buster['products'].filter( (item) => { return item.gender == gender; }, gender);
	}
	
	updateQuantityInCart (item) {
		if( item.quantity > -1 ) {
			item.quantity = ( item.counter == 1 ) ? item.quantity + 1 : item.quantity - 1;
			let doAction = this.customer.updateQuantityInCart(item);
			if ( doAction ) {
				this.store.updateCart({productId: item.id, quantity: item.quantity}).subscribe(res=>{}, (err: HttpErrorResponse) => {});
				this.getMyCart();
			} else {
				this.toastr.warning("Sorry, Cart not updated!");
			}
		} else {
			this.toastr.warning("Sorry, Quantity should be numeric and greater then one!");
		}	
	}

	removeItemFromCartPopup(item){
		this.removeItem = item;
		this.confimMsg = 'Are you sure, you want to delete this product?';
		return true;
	}

	removeItemFromCart () {
		if ( this.removeItem['cart_id'] ) {
			this.confimMsg = 'Waiting ...';
			this.store.removeCart(this.removeItem['cart_id']).subscribe(
				res => {
				  if( res.status ){
					this.hideDeleteModal.nativeElement.click();
					this.track.removeFromCart(this.removeItem);
					this.removeItem = {};
					this.getMyCart();
				  }else{
					this.confimMsg = res.message;
				  }
				},
				(err: HttpErrorResponse) => {
				  this.toastr.warning("Sorry, there are some app issue!");
				}
			);
		} else {
			if ( this.customer.removeFromCart(this.removeItem) ) {
				this.getMyCart();
				this.hideDeleteModal.nativeElement.click();
			} else {
				this.toastr.warning("Sorry, Item not deleted!");
			}
		}
	}

	onSelectionChange(value:number, code:string){
		this.inputData['paymentMethod'] = value;
		this.methodForm.patchValue({paymentMethod:value});
		this.finalMessage = "";
		this.store.setCartInfo(this.inputData);
		this.getMyCart();
		return false;
	}
    
	getOtp () {
        this.finalMessage = '';
        this.otpResponse['message'] = 'Waiting...<i class="fa fa-spinner fa-spin"></i>';
        this.otpResponse['textClass'] = 'text-warning';
        const formData = {
            userId: this.userId,
            name  : this.selectedAddress['firstname'] + ' ' + this.selectedAddress['lastname'],
            email : this.selectedAddress['email'],
            mobile: this.selectedAddress['mobile'],
            amount: this.grandFinalTotal
        };
        this.store.getOtp(formData).subscribe(
            res => {
                if ( res.status ) {
                    this.otpResponse['textClass'] = 'text-success';
                } else {
                    this.otpResponse['textClass']= 'text-danger';
                }
                this.otpResponse['message'] = res.message;
            }, (err: HttpErrorResponse) => {
                this.otpResponse['textClass'] = 'text-danger';
                this.otpResponse['message'] = 'Sorry, there are some app issue!';
            }
        );
        return true;
    }

    verifyOtp () {
        this.otpResponse['message'] = 'Waiting...<i class="fa fa-spinner fa-spin"></i>';
        this.otpResponse['textClass'] = 'text-warning';
        if ( this.otpResponse['otp'] !== 0 ) {
            const formData = {
                userId: this.userId,
                otp: this.otpResponse['otp']
            };
            this.store.verifyOtp(formData).subscribe(
                res => {
                    if ( res.status ) {
                        this.otpResponse['textClass'] = 'text-warning';
						this.elem.nativeElement.querySelector('#closeOtpPopup').click();
                        this.placeOrder(); // place order just after otp verification
                    } else {
                        this.otpResponse['textClass'] = 'text-danger';
                    }
                    this.otpResponse['message'] = res.message;
                }, (err: HttpErrorResponse) => {
                    this.otpResponse['textClass'] = 'text-danger';
                    this.otpResponse['message'] = 'Sorry, there are some app issue!';
                }
            );
        } else {
            this.otpResponse['textClass'] = 'text-danger';
            this.otpResponse['message'] = 'Please enter otp number!';
        }
        return false;
    }
	
	checkData(){
		this.finalMessage = '';
		this.finalStatus = true;
		
		if ( this.selectedAddress['firstname'] == '' || this.selectedAddress['lastname'] == '' || this.selectedAddress['address'] == '' || this.selectedAddress['pincode'] == '' || this.selectedAddress['city'] == '' || this.selectedAddress['email'] == '' || this.selectedAddress['mobile'] == '' ) {
			this.finalMessage = 'Please select shipping address!';
			this.finalStatus = false;
		}
		if( this.methodForm.value.paymentMethod == '' || this.methodForm.value.paymentMethod == 0 ){
			this.methodForm.controls.paymentMethod.markAsDirty();
			this.finalStatus = false;
		}
		//console.log(this.methodForm.value.paymentMethod);
		if ( this.finalStatus ){
			this.store.checkPincode(this.selectedAddress['pincode']).subscribe(
				res => {
					this.pincodeStatus = res.status; // 1 both, 2 prepaid, 3 postpaid, 0 not
					if( this.pincodeStatus > 0 ){
						this.finalMessage = 'Waiting...';
						if( this.inputData['paymentCode'] == 'cod' ){
							this.getOtp();
							this.elem.nativeElement.querySelector('#getOtpPopup').click();
						} else {
							this.placeOrder();
						}
					}else{
						this.deliveryMessage = '';
						this.finalStatus = false;
						this.finalMessage = "Sorry, service not available at pincode: "+this.selectedAddress['pincode'];
					}					
				},
				(err: HttpErrorResponse) => {
				}
			);
			
		}
		return true;
	}

	placeOrder(){
		if( this.orderPlaceStatus == 0 ) {
			//shipping address
			this.inputData['shipping_firstname'] = this.selectedAddress['firstname'];
			this.inputData['shipping_lastname'] = this.selectedAddress['lastname'];
			this.inputData['shipping_address']	= this.selectedAddress['address'];
			this.inputData['shipping_city'] = this.selectedAddress['city'];
			this.inputData['shipping_state'] = this.selectedAddress['state'];
			this.inputData['shipping_country'] = this.selectedAddress['country'];
			this.inputData['shipping_pincode'] = this.selectedAddress['pincode'];
			this.inputData['shipping_email'] = this.selectedAddress['email'];
			this.inputData['shipping_mobile'] = this.selectedAddress['mobile'];
			
			//console.log(this.inputData);
			this.orderPlaceStatus = 1;
			this.store.saveOrderDetails(this.inputData).subscribe(
			  res => {
				this.orderPlaceStatus = 0;
				if ( res.status ) {
				  this.orderId = res.data.orderNumber;
				  this.paymentGatewayUrl = res.data.paymentGatewayUrl;
				  this.store.setSuccessData({orderNumber:this.orderId, orderTotal: this.grandFinalTotal, orderPrefix: res.data.orderPrefix, orderCurrency: this.cartCurrency, trackFlag:1, orderMessage: res.message});
				  let submitFormGateway = 1;
				  setInterval(() => { if( submitFormGateway == 1 ){ this.elem.nativeElement.querySelector('#paymentForm').submit(); submitFormGateway = 0; } }, 2000);
				  
				} else {
				  this.finalMessage = res.message;
				}
			  },
			  (err: HttpErrorResponse) => {
				if(err.error instanceof Error){
				  console.log('Client Error: '+err.error.message);
				}else{
				  console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
				}
			  }
			);
		} else {
			this.finalMessage = 'Please wait process running...';
		}
		return false;
	}

    upperToLower(event, fieldName){
	    (<FormControl>this.addressForm.controls[fieldName]).setValue(event.target.value.toLowerCase(), {});
	}

}
