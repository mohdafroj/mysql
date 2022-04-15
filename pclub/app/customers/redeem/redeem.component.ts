import { Component, OnInit, ElementRef, ViewEncapsulation } from '@angular/core';
import { DomSanitizer } 			from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } 					from '@angular/forms';
import { Router, ActivatedRoute, NavigationEnd } 				from '@angular/router';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { Myconfig } 											from './../../_services/pb/myconfig';
import { ProductsService } 										from './../../_services/pb/products.service';
import { CustomerService } 										from './../../_services/pb/customer.service';
import { StoreService } 										from './../../_services/pb/store.service';
import { TrackingService } 										from './../../_services/tracking.service';
import { DataService } 											from './../../_services/data.service';
import { ToastrService } 										from 'ngx-toastr';

@Component({
  selector: 'pc-redeem',
  templateUrl: './redeem.component.html',
  styleUrls: ['./redeem.component.css'],
  encapsulation: ViewEncapsulation.None

})
export class RedeemComponent implements OnInit {
	rForm:FormGroup;  
	userId:number							= 0;
	productList								= [];
	resultMsg:string						= '';
	resultStatus:number						= 0;
	serverRequest = false;
	pincodeMessage = '';
	pincodeStep = 0;
	pincodeStatus
	redeemQuantity = 0;
	redeemProduct = [];
	addressId = 0;
	addressResponse	= {};
	addresses = [];
	selectedAddress = {};
	newAddress = 0;
	initAddress	= {};
	tabIndex = 1;
	orderId = 0;
	orderMessage = '';
	sanitizer:any;
	constructor(
		private toastr:ToastrService, 
		private router: Router, 
		private route: ActivatedRoute, 
		private product: ProductsService, 
		private customer: CustomerService, 
		private store: StoreService, 
		private config:Myconfig, 
		private elem:ElementRef, 
		private track:TrackingService,
		private dataService: DataService,
		private sanitize:DomSanitizer
	) {
		this.sanitizer = sanitize;
	}

	ngOnInit() {
		this.userId = this.customer.getId();		
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
		this.initAddressForm(this.initAddress);
		this.getAddresses();
		this.getRedeemProducts();
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

	nextButton () {
		this.tabIndex=1; 
		if ( this.addresses['address'].length > 0 ) { this.newAddress = 0; }
	}
	
	getAddresses() {
		this.customer.getAddresses().subscribe(
			res => {
				this.addresses = res.data; //this.addresses['address'] = [];
				if( this.addresses['address'].length > 0 ){
					for( let item of this.addresses['address'] ){
						if( item.set_default == "1" ){
							this.selectedAddress = item;
							this.rForm.patchValue({setdefault: item.id});
							//this.elem.nativeElement.querySelector('#RevieworederSelected').click();
							break;
						}
					}
				}else{
					this.addressId = 0;
					this.newAddress = 1;
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
		this.newAddress = 1;
		this.addressId = item.id;
		this.initAddressForm(item);
	}
	
	cancelAddress(){
		this.newAddress = 0;
		this.addressId = 0;
		this.initAddressForm(this.initAddress);
	}

	addressMessageClear(){
		//this.addressResponse = {};
	}

	addNewAddress(){
		this.newAddress = 1;
		this.addressId = 0;
		this.initAddressForm(this.initAddress);
	}
	
	saveAddress(formData){ 
		let checkForm = true;
		if ( formData['firstname'] == '' || formData['lastname'] == '' || formData['address'] == '' || formData['pincode'] == '' || formData['city'] == '' || formData['email'] == '' || formData['mobile'] == '' ) {
			checkForm = false;
		}  //console.log(checkForm, this.serverRequest, formData);
		if( (this.serverRequest == false) && ( checkForm == true ) && ( this.redeemProduct.length > 0 ) ) {
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
									this.addressId = 0;
									this.tabIndex = 2;
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
		} else if ( this.redeemProduct.length > 0 ) {
			this.addressResponse = { message: 'Please select product to redeem!', textClass:'text-danger' };
		} else if ( checkForm == false ) {	
			this.addressResponse = { message: 'Please fill required fields!', textClass:'text-danger' };
		} else {
			this.addressResponse = { message:'Please wait...', textClass:'text-danger' };
		}
	}
	
	getRedeemProducts(){
		this.productList	= [];
		this.resultMsg      = 'Loading...';
		this.resultStatus   = 0;
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		this.product.getRedeemProducts(prms).subscribe(
            res => {
                if(res.status){
					this.productList = res.data['products'];
					this.redeemQuantity = res.data['redeemQuantity'];
				}
				this.resultMsg = res.message;
				this.resultStatus = 1;
				if ( this.redeemQuantity == 0 ) {
					this.router.navigate(['customer/refer-and-earn']);
				}
				//console.log(this.redeemQuantity);
            },
            (err: HttpErrorResponse) => {
                if(err.error instanceof Error){
					this.resultMsg = err.error.message;
                }else{
					this.resultMsg = JSON.stringify(err.error);
                }
				this.resultStatus = 1;
            }
        );
    }

	selectedProduct (item) {
		return this.redeemProduct.some(function(el){ return el.id === item.id });
	}
	
	selectProduct(item) {
		this.tabIndex = 1;
		this.orderId = 0;
		let totalItem = this.redeemProduct.reduce(function(total, itemqty){ return total + itemqty.cart_quantity ;}, 0);
		if ( (this.redeemQuantity > totalItem) && !this.redeemProduct.some(function(el){ return el.id === item.id }) ) {
			item.cart_quantity = 1;
			this.redeemProduct.push(item);
			this.track.addToCart(item);
		} else {
			this.toastr.error('Sorry, you can\'t redeem more than '+`${this.redeemQuantity}`+' quantity.');
		}
	}
	
	removeProduct(item) {
		this.redeemProduct = this.redeemProduct.filter(p => item.id !== p.id);
		this.track.removeFromCart(item); 
	}
	
	productQty (item, flag) {
		let totalItem = this.redeemProduct.reduce(function(total, itemqty){ return total + itemqty.cart_quantity ;}, 0);
		if ( flag == '+1' ) {
			if ( totalItem < this.redeemQuantity ) {
				this.redeemProduct.some(function(el){
					if ( item.id = el.id ) {
						item.cart_quantity += 1; 
					}
					return item; 
				});
			} else {
				this.toastr.error('Sorry, you can\'t redeem more than '+`${this.redeemQuantity}`+' quantity.');
			}
		} else {
			if ( item.cart_quantity > 1 ) {
				this.redeemProduct.some(function(el){
					if ( item.id = el.id ) {
						item.cart_quantity -= 1; 
					}
					return item; 
				});
			} else {
				this.toastr.error('Sorry, you should keep at least 1 quantity.');
			}
		}	
	}
	
	redeemOrder () {
		if ( !this.serverRequest ) {
			this.serverRequest == true;
			let formData = {product: this.redeemProduct, address: this.selectedAddress};
			this.customer.redeemOrder(formData).subscribe(
			  res => {
				if( res.status ){
				  this.orderId = res.data.orderNumber;
				  this.orderMessage = res.message;
				  this.initAddressForm(this.initAddress);
				  this.tabIndex = 3;
				  let redeemTrackObj = { 'order_number': this.orderId };
				  redeemTrackObj['shopping']['cart'] = this.redeemProduct;
				  this.store.setTrackingData(redeemTrackObj);
				  this.track.storeTrack('purchase');
				  this.selectedAddress = {};
				  this.redeemProduct = [];
				} else {
				  this.toastr.error(res.message);
				}
				this.serverRequest == true;
			  },
			  (err: HttpErrorResponse) => {
				if(err.error instanceof Error){
				  console.log('Client Error: '+err.error.message);
				}else{
				  console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
				}
				this.serverRequest == true;
			  }
			);
		} else {
			this.toastr.warning("Please wait...");
		}
	}
	
}
