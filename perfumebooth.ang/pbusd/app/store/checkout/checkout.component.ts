import { Component, OnInit, ViewChild, ElementRef, HostListener } from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } 			from '@angular/platform-browser';
import { FormGroup, FormControl, Validators, RequiredValidator } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';

import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { TrackingService } from './../../_services/tracking.service';

import {composeValidators} from "@angular/forms/src/directives/shared";

//import './../../../assets/js/checkout.js';

@Component({
  selector: 'app-checkout',
  templateUrl: './checkout.component.html',
  styleUrls: [
		'./checkout.component.css'
	]
})
export class CheckoutComponent implements OnInit {
  @ViewChild('hideDeleteModal') hideDeleteModal: ElementRef;
  rForm:FormGroup;
  methodForm:FormGroup;
  addresses:any							=[];
  selectedAddress:any					={};
  initAddress:any						={};
	editedAddressId:number				=-1;
	addressIndex:number						=-1;
	addressResponse							={};
	tabIndex:any						= {};								
  userId:number							=0;
  orderId:number						=0;
  customerAuth:string                   ='';
  inputData:any							=[];

  paymentMethodData						=[];
  myCart:any							=[];
  customer:any							=[];
  credits:any							=[];
  discounts:any							=[];
  prive:any								=[];
  
  couponCode:string						='';
  voucherMessage:string					='';

  shippingAmount:number					=0;
  codAmount:number						=0;
  grandFinalTotal:number				=0;
  pincodeStatus:number					=0;

  finalStatus:boolean					=false;
  finalMessage:string					='';

  otpResponse:any						={};
  
  enterOtpNumber:any					='';
  summaryClass:string					= '';
  paymentGatewayUrl:string				= '';
  sendFormDataToPaymentGateway:number   = 0;
  orderPlaceStatus:number               = 0;
  currencyLogo:string                   = '$';
  SUBDIR:string;
  sanitizer:any;
	constructor(
		private router: Router, 
		private route: ActivatedRoute, 
		public auth: CustomerService, 
		private store: StoreService, 
		private config:Myconfig, 
		private elem:ElementRef, 
		private track:TrackingService,
		private sanitize:DomSanitizer
	){
		this.sanitizer = sanitize;
		this.tabIndex.third = 0;
		
		this.initAddress = {
			id:0,
			firstname:'',
			lastname:'',
			address:'',
			city:'',
			state:'',
			country:'USA',
			pincode:'',
			email:'',
			phone:'',
			set_default:0
		};
	}

	ngOnInit() {
		this.config.scrollToTop(0, 0);
		this.SUBDIR = this.config.SUBDIR;
		this.userId = this.auth.getId();
		this.customerAuth = this.auth.getToken();
		let cartInfo:any = JSON.parse(localStorage.getItem('usdcartInfo'));
		cartInfo = (cartInfo != null) ? cartInfo : {};  
		cartInfo.trackPage = 'checkout';
		this.inputData = cartInfo;
		this.getMyCart();
		this.initAddressForm(this.initAddress);
		this.getAddresses(); //this.inputData.paymentMethod = 1;
	    this.methodForm = new FormGroup ({
			paymentMethod: new FormControl(this.inputData.paymentMethod, Validators.required)
		});

		//this.otpResponse.message = 'Waiting...<i class="fa fa-spinner fa-spin"></i>';
		//this.otpResponse.class = 'loader_msz';
		//this.elem.nativeElement.querySelector('#getOtpPopup').click();
	}

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
		if ( this.addresses.states ){
			for( let item of this.addresses.states ){
				if( item.title == this.rForm.value.state ){
					stateValue = item.title;
					break;
				}
			}		
			if( stateValue == '' ) {
				stateValue = this.addresses.states[0]['title'] ? this.addresses.states[0]['title'] : '';
			}
		}
		this.rForm.patchValue({state: stateValue});
	
		let countryValue = '';
		if ( this.addresses.locations ){
			for( let item of this.addresses.locations ){
				if( item.title == this.rForm.value.country ){
					countryValue = item.title;
					break;
				}
			}		
			if( countryValue == '' ) {
				countryValue = this.addresses.locations[0]['title'] ? this.addresses.locations[0]['title'] : '';
			}
		}
		this.rForm.patchValue({country: countryValue});
	
	}
	
	getAddresses() {
		this.auth.getAddresses().subscribe(
			res => {
				this.addresses = res.data; //this.addresses.address = [];
				if( this.addresses.address.length > 0 ){
					for( let item of this.addresses.address ){
						if( item.set_default == "1" ){
							this.selectedAddress = item;
							this.rForm.patchValue({setdefault:item.id});
							//this.elem.nativeElement.querySelector('#RevieworederSelected').click();
							break;
						}
					}
				}else{
					this.editedAddressId = 0;
					this.initAddressForm(this.initAddress);
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	
	editAddress(item){
		this.editedAddressId = item.id;
		this.initAddressForm(item);
	}
	
	cancelAddress(){
		this.editedAddressId = -1;
		this.initAddressForm(this.initAddress);
	}

	addressMessageClear(){
		//this.addressResponse = {};
	}

	addNewAddress(){
		this.editedAddressId = 0;
		this.initAddressForm(this.initAddress);
	}

	saveAddress(formData, addressIndex){
		this.addressIndex 		= addressIndex;
		this.addressResponse 	= {id:this.editedAddressId,addressIndex:this.addressIndex, message:'<i class="fa fa-spinner fa-spin"></i>', class:'loader_msz'};
		let countryCode2:string = '';
		for( let item of this.addresses.locations ){
			if( item.title == formData.country ){ countryCode2 = item.code2; }
		}
		if( countryCode2 != '' ){
			this.store.checkPincode(countryCode2).subscribe(
				res => {
					this.pincodeStatus = res.status; // 1 both, 2 prepaid, 3 postpaid, 0 not
					if( this.pincodeStatus > 0 ){
						this.addressResponse = {};
						formData.setdefault = 1;
						this.auth.addAddresses(formData).subscribe(
							res => {
								if( res.status ){
									//this.getAddresses();
									this.config.scrollToTop(0, 0);
									this.addresses = res.data;
									if( this.addresses.address.length > 0 ){
										for( let item of this.addresses.address ){
											if( item.set_default == "1" ){
												this.selectedAddress = item;
												this.rForm.patchValue({setdefault:item.id});
												//this.elem.nativeElement.querySelector('#RevieworederSelected').click();
												break;
											}
										}
									}
									this.initAddressForm(this.initAddress);
									this.addressResponse = {id:this.editedAddressId,addressIndex:this.addressIndex, class:'success_msz'};
									this.editedAddressId = -1;
									this.tabIndex.third = 1;
									this.elem.nativeElement.querySelector('#RevieworederSelected').click();
									//this.addressResponse 	= {id:this.editedAddressId,addressIndex:this.addressIndex, message:'<i class="fa fa-spinner fa-spin"></i>', class:'loader_msz'};
									this.inputData.countryCode2 = countryCode2;
									this.getMyCart();
								}else{
									this.addressResponse = {id:this.editedAddressId,addressIndex:this.addressIndex,message:res.message, class:'error_msz'};
								}
							},
							(err: HttpErrorResponse) => {
								var message = '';
								if(err.error instanceof Error){
									message = 'Client error: '+err.error.message;
								}else{
									message = 'Server error: '+JSON.stringify(err.error);
								}
								this.addressResponse = {id:this.editedAddressId,addressIndex:this.addressIndex,message:message, class:'error_msz'};
							}
						);
					}else{
						this.addressResponse = {id:this.editedAddressId,addressIndex:this.addressIndex,message:res.message, class:'error_msz'};
					}
				},
				(err: HttpErrorResponse) => {
					this.addressResponse = {id:this.editedAddressId,addressIndex:this.addressIndex,message:'Sorry, may be network issue, please refresh page!', class:'error_msz'};
				}
			);
		}else{
			
		}
	}
	
	reviewedCartContinue(){
		this.tabIndex.fourth = 1;
		this.elem.nativeElement.querySelector('#PaymentoptionSelected').click();
	}

	getMyCart(){	  
		this.store.getCart(this.inputData).subscribe(
			res => {
				if(res.status && res.data.cart != undefined){
					this.myCart 					= res.data.cart; //console.log(this.myCart.location);
					this.paymentMethodData     		= res.data.payment_method_data;
					this.customer 					= res.data.customer;
					this.credits 					= res.data.credits;
					this.discounts 					= res.data.discounts;
					this.prive 						= res.data.prive;
					this.currencyLogo               = res.data.cart.location.logo;
					this.couponCode			 		= res.data.coupon_code;
					this.shippingAmount		 		= res.data.shipping_amount;
					this.codAmount			 		= res.data.payment_fees;
					this.grandFinalTotal		 	= res.data.grand_final_total;
				    //console.log(res);
					if( this.inputData.paymentMethod != res.data.payment_method ){
						this.inputData.paymentMethodSelected = '';
						this.methodForm.patchValue({paymentMethod:''});
					}
					if(this.inputData.paymentMethod != 1){
						this.inputData.otpStatus = true;
					}else{
						this.inputData.otpStatus 	= (this.inputData.mobile == this.selectedAddress.mobile) ? true:false;
					}
					localStorage.setItem('trackingData',JSON.stringify(res.data));
					this.track.trackCheckout();
				}else{
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
		this.inputData.paymentMethod = value;
		this.methodForm.patchValue({paymentMethod:value});
		for(let i of this.paymentMethodData){
			if( i.id == value ){
				this.inputData.paymentMethodSelected = i.title;
				break;
			}
		}
		this.finalMessage = "";
		localStorage.setItem('usdcartInfo',JSON.stringify(this.inputData));
		this.getMyCart();
		return false;
	}

	checkData(){
		this.finalMessage = '';
		this.finalStatus = true;
		
		if( this.selectedAddress.firstname == '' || this.selectedAddress.lastname == '' || this.selectedAddress.address == '' || this.selectedAddress.pincode == '' || this.selectedAddress.city == '' || this.selectedAddress.email == '' || this.selectedAddress.mobile == '' ){
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
				this.finalMessage = "Wait...";
				this.placeOrder();
			}else{
				this.finalStatus = false;
				this.finalMessage = "Sorry, service not available at pincode: "+this.selectedAddress.pincode;
			}
		}
		return true;
	}

	placeOrder(){
		if( this.orderPlaceStatus == 0 ){
			this.finalMessage = 'Waiting...';
			//shipping address
			this.inputData.shipping_firstname 	= this.selectedAddress.firstname;
			this.inputData.shipping_lastname 	= this.selectedAddress.lastname;
			this.inputData.shipping_address 	= this.selectedAddress.address;
			this.inputData.shipping_city 		= this.selectedAddress.city;
			this.inputData.shipping_state 		= this.selectedAddress.state;
			this.inputData.shipping_country 	= this.selectedAddress.country;
			this.inputData.shipping_pincode 	= this.selectedAddress.pincode;
			this.inputData.shipping_email 		= this.selectedAddress.email;
			this.inputData.shipping_mobile 		= this.selectedAddress.mobile;
			
			//console.log(this.inputData);
			this.orderPlaceStatus = 1;
			this.sendFormDataToPaymentGateway = 1;
			this.store.saveOrderDetails(this.inputData).subscribe(
			  res => {
				this.orderPlaceStatus = 0;
				if( res.status ){
				  this.orderId = res.data.orderNumber;
				  this.track.setOrderNumberToTrack(this.orderId);
				  this.paymentGatewayUrl = res.data.paymentGatewayUrl;
				  localStorage.setItem('usdSuccessData', JSON.stringify({'orderNumber':this.orderId, 'trackFlag':1}));
				  setInterval(() => { if( this.sendFormDataToPaymentGateway == 1 ){ this.elem.nativeElement.querySelector('#paymentForm').submit(); this.sendFormDataToPaymentGateway = 0; } }, 2000);
				  
				}else{
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
		}	
		return false;
	}

    upperToLower(event, fieldName){
	    (<FormControl>this.rForm.controls[fieldName]).setValue(event.target.value.toLowerCase(), {});
	}
	
	showSummary(){
		this.summaryClass = 'filter-is-visible';
	}
	hideSummary(){
		this.summaryClass = '';
	}
	
	@HostListener('window:click', ['$event'])
    checkClick() {
      const componentPosition = this.elem.nativeElement.offsetTop
      const scrollPosition = window.pageYOffset
    }

}
