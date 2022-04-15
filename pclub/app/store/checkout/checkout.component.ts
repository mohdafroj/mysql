import { Component, OnInit, DoCheck, ViewChild, ViewEncapsulation, ElementRef, HostListener } from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } 			from '@angular/platform-browser';
import { FormGroup, FormControl, Validators, RequiredValidator } from '@angular/forms';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { ToastrService } from 'ngx-toastr';

import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { TrackingService } from './../../_services/tracking.service';

@Component({
  selector: '[pc-checkout]',
  templateUrl: './checkout.component.html',
  styleUrls: [
		'./checkout.component.css'
	],
	encapsulation: ViewEncapsulation.None
})
export class CheckoutComponent implements OnInit, DoCheck {
	@ViewChild('hideDeleteModal', {static: false}) hideDeleteModal: ElementRef;
	rForm:FormGroup;  
	methodForm:FormGroup;
	userTypeForm:FormGroup;
	loginForm:FormGroup;
	registerForm:FormGroup;
	shoppingCart = [];
	shoppingPack = [];
	cartCurrency;
	cartTotal = 0.00;
	serverRequest = false;
	addresses = [];
	selectedAddress = {};
	initAddress	= {};
	addressId	= -1;
	addressResponse	= {};
	tabIndex = 1;								
	userId	= 0;
	orderId = 0;
	customerAuth = '';
    inputData = {};

  paymentMethodData						=[];
  credits:any							=[];
  discounts:any							=[];
  customerInfo = [];
  couponCode:string						='';

  shippingAmount:number					=0;
  codAmount:number						=0;
  grandFinalTotal:number				=0;
  pincodeStatus:number					=0;

  finalStatus:boolean					=false;
  finalMessage:string					='';

  otpResponse = {};
  
  enterOtpNumber:any					='';
  summaryClass:string					= '';
  paymentGatewayUrl:string				= '';
  orderPlaceStatus:number               = 0;
  
  loginStatusType = 'olduser';
  tokenForAccount = '';
  myFormData: any;
  isEmail = 0;
  isStep;
  resObj;
  oldUsername;
  oldEmail;
  oldMobile;
  oldOtp;
    
  sanitizer:any;
	constructor(
		private toastr: ToastrService,
		private router: Router, 
		private route: ActivatedRoute, 
		public customer: CustomerService, 
		private store: StoreService, 
		private config:Myconfig, 
		private elem:ElementRef, 
		private track:TrackingService,
		private sanitize:DomSanitizer
	){
		this.sanitizer = sanitize;
		this.initAddress = {
			id:0,
			firstname:'',
			lastname:'',
			address:'',
			city:'',
			state:'',
			country:'India',
			pincode:'',
			email:'',
			mobile:'',
			set_default:0
		};
		
	}

	ngOnInit() {
		this.getBack();
		this.addressId	= -1;
		this.cartCurrency = this.config.cartCurrency;
		this.config.scrollToTop(0, 0);
		this.paymentGatewayUrl = this.config.paymentGatewayUrl;
		this.userId = this.customer.getId();
		this.customerAuth = this.customer.getToken();
		this.inputData = this.store.getCartInfo();
		this.inputData['trackPage'] = 'checkout';
		this.inputData['paymentMethod'] = this.inputData['paymentMethod'] ? this.inputData['paymentMethod'] : 1;
		this.inputData['paymentCode'] = this.inputData['paymentCode'] ? this.inputData['paymentCode'] : '';
		this.getMyCart();
		this.initAddressForm(this.initAddress);
		this.getAddresses(); //this.inputData.paymentMethod = 1;
	    this.methodForm = new FormGroup ({
			paymentMethod: new FormControl(this.inputData['paymentMethod'], Validators.required)
		});
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
	}

	//Start of login/guest section
	
	loginUsernameValidator (control) {
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		let MOBILE_REGEXP = /^[1-9]{1}[0-9]{9}$/;
		if( !EMAIL_REGEXP.test(control.value) ) {
			if (!MOBILE_REGEXP.test(control.value)) {
				return {'username': true};
			}
		}
	}
	
	getBack() {
		this.isStep = 1;
		this.oldUsername = this.oldEmail = this.oldMobile = this.oldOtp = '';
		this.resObj = {message:'', textClass:'', otpMessage:'', otpClass:''};
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
		
	//End of login/guest section
	//init address form
	initAddressForm(value) {
	    this.rForm = new FormGroup ({
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
				if( item.title == this.rForm.value.state ){
					stateValue = item.title;
					break;
				}
			}		
			if( stateValue == '' ) {
				stateValue = this.addresses['states'][0]['title'] ? this.addresses['states'][0]['title'] : '';
			}
		}
		this.rForm.patchValue({state: stateValue});
	
		let countryValue = '';
		if ( this.addresses['locations'] ){
			for( let item of this.addresses['locations'] ){
				if( item.title == this.rForm.value.country ){
					countryValue = item.title;
					break;
				}
			}		
			if( countryValue == '' ) {
				countryValue = this.addresses['locations'][0]['title'] ? this.addresses['locations'][0]['title'] : '';
			}
		}
		this.rForm.patchValue({country: countryValue});
	
	}
	
	getAddresses() {
		this.customer.getAddresses().subscribe(
			res => {
				this.addresses = res.data; //this.addresses['address'] = [];
				if( this.addresses['address'] && this.addresses['address'].length > 0 ){
					for( let item of this.addresses['address'] ){
						if( item.set_default == "1" ){
							this.selectedAddress = item;
							this.rForm.patchValue({setdefault: item.id});
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
									this.tabIndex = 2;
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
	
	getMyCart () {
		let myCart = this.customer.getFromCart();
		this.inputData['shopping'] = myCart['shopping'];
		this.store.getCart(this.inputData).subscribe(
			res => {
				if ( res.status && res.data['shopping'] ) {
					this.cartCurrency   = res.data.currency;
					this.cartTotal   	= res.data.cart_total;
					this.shoppingCart 	= res.data.shopping.cart;
					this.shoppingPack 	= res.data.shopping.pack;
					this.customerInfo 	= res.data.customer;
					this.paymentMethodData     		= res.data.payment_method_data;
					this.credits 					= res.data.credits;
					this.discounts 					= res.data.discounts;
					this.couponCode			 		= res.data.coupon_code;
					this.shippingAmount		 		= res.data.shipping_amount;
					this.codAmount			 		= res.data.payment_fees;
					this.grandFinalTotal		 	= res.data.grand_final_total;
				    //console.log(this.shoppingCart);
					if( this.inputData['paymentMethod'] != res.data.payment_method ){
						this.inputData['paymentMethodSelected'] = '';
					}
					for(let i of this.paymentMethodData){
						if( i.id == this.inputData['paymentMethod'] ){
							this.inputData['paymentMethodSelected'] = i.title;
							this.inputData['paymentCode'] = i.code;
							break;
						}
					}
					this.methodForm.patchValue({paymentMethod: this.inputData['paymentMethod']});
					this.store.setTrackingData(res.data);
					this.track.storeTrack('checkout'); //call checkout page event
				} else {
					this.router.navigate(['/checkout/cart'],{});
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
	}

	onSelectionChange(value:number){
		this.inputData['paymentMethod'] = value;
		this.methodForm.patchValue({paymentMethod:value});
		for(let i of this.paymentMethodData){
			if( i.id == value ){
				this.inputData['paymentMethodSelected'] = i.title;
				this.inputData['paymentCode'] = i.code;
				break;
			}
		}
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
			if( this.pincodeStatus > 0 ){
				this.finalMessage = 'Waiting...';
				if( this.inputData['paymentCode'] == 'cod' ){
                    this.getOtp();
                    this.elem.nativeElement.querySelector('#getOtpPopup').click();
				} else {
					this.placeOrder();
				}
			}else{
				this.finalStatus = false;
				this.finalMessage = "Sorry, service not available at pincode: "+this.selectedAddress['pincode'];
			}
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
	    (<FormControl>this.rForm.controls[fieldName]).setValue(event.target.value.toLowerCase(), {});
	}
	
	@HostListener('window:click', ['$event'])
    checkClick() {
      const componentPosition = this.elem.nativeElement.offsetTop
      const scrollPosition = window.pageYOffset
    }

}
