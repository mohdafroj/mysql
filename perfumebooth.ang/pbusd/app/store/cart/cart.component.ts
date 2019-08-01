import { Component, OnInit, DoCheck, ViewChild, ElementRef } 						from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } 			from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } 								from '@angular/forms';
import { Router, ActivatedRoute, NavigationEnd } 							from '@angular/router';
import { HttpErrorResponse } 												from '@angular/common/http';

import { Myconfig } 														from './../../_services/pb/myconfig';
import { CustomerService } 														from '../../_services/pb/customer.service';
import { StoreService } 													from './../../_services/pb/store.service';
import { TrackingService } 													from './../../_services/tracking.service';

//declare let gtag: Function;

@Component({
  selector: 'app-cart',
  templateUrl: './cart.component.html',
  styleUrls: [
		'./../../../assets/css/responsive-table.css',
		'./../../../assets/css/addcart.css',
		'./cart.component.css'
	]
})
export class CartComponent implements OnInit,DoCheck {
  @ViewChild('hideAccountModal') hideAccountModal: ElementRef;
  @ViewChild('hideDeleteModal') hideDeleteModal: ElementRef;
  
  couponForm:FormGroup;
  myCart:any							={cart_total:0};
  customer:any							=[];
  credits:any							=[];
  discounts:any							=[];
  prive:any								=[];
  
  accMsg:string							='';
  userId:number							=0;
  couponCode:string						='';
  couponMsg:string						='';
  totalAmountAfterDiscount:number		=0;

  shippingAmount:number					=0;
  grandTotal:number						=0;
  addresses:any							=[];

  confimMsg:string						='';
  cartId:number							=0;
  inputData:any							=[];
  optionStatus:number 					=1;
	loaderMessage:string					='';
	
  loginForm:FormGroup;
	myFormData:any;
	oldUsername:any;
	oldOtp:any;
	isEmail:number 	= 0;
	isStep:number 	= 1;
	resObj:any 		= {};
	sanitizer:any;
	constructor(
		private router: Router, 
		private route: ActivatedRoute, 
		private config:Myconfig, 
		private auth:CustomerService, 
		private store:StoreService, 
		private track:TrackingService, 
		private sanitize:DomSanitizer)
	{
		this.sanitizer = sanitize;
	}

	ngOnInit() {
		this.config.scrollToTop();
		this.userId = this.auth.getId();
		this.initLoginForm("", "", 0);
		this.couponForm = new FormGroup ({
			inCouponCode: new FormControl("")
		});
		let cartInfo = localStorage.getItem('usdcartInfo');
		if(cartInfo != null){
			cartInfo = JSON.parse(cartInfo);
			this.inputData = cartInfo;
			this.optionStatus = this.inputData.optionStatus;
		}else{
			this.inputData = {
				couponCode:'',
				trackPage:'cart',
				giftVoucherStatus:true,
				pbPointsStatus:false,
				pbCashStatus:true
			};
		}
		this.getMyCart();
	}

	getMyCart(){
		let trackData:string = localStorage.getItem('trackingData');
		if( trackData != null ){
			localStorage.removeItem('trackingData');
		}
		localStorage.setItem('usdcartInfo',JSON.stringify(this.inputData));
		this.loaderMessage				= "Loading...";
		//var response = this.store.getCart(this.inputData);
		//console.log(response);
		
		this.store.getCart(this.inputData).subscribe(
			res => {
				if(res.status){
					if( res.data['cart'] ){
						this.myCart 					= res.data.cart;
						this.customer 					= res.data.customer;
						this.credits 					= res.data.credits;
						this.discounts 					= res.data.discounts;
						this.prive 						= res.data.prive;
						
						this.couponCode 				= res.data.coupon_code;
						this.couponMsg 					= res.data.coupon_msg;
						this.totalAmountAfterDiscount 	= res.data.total_amount_after_discount;
						this.shippingAmount 			= res.data.shipping_amount;
						this.grandTotal 				= res.data.grand_total_at_cart;
						
					
					}else{
						this.myCart 					= {cart_total:0};
						this.loaderMessage				= "Your cart is empty!";
					}
					this.auth.setCart(this.myCart.cart); //update cart data in logged status
					if(this.discounts.points > 0){
						this.inputData.giftVoucherStatus = false;
						this.inputData.pbPointsStatus = true;
						this.inputData.optionStatus = 2;
					}
					
					if( this.couponCode == "" ){
						this.inputData.couponCode   = "";
					}
					if( this.inputData.paymentMethod == undefined){
						this.inputData.paymentMethod = res.data.payment_method;
					}
					localStorage.setItem('usdcartInfo',JSON.stringify(this.inputData));						
				}else{
					this.myCart 					= {cart_total:0};
					this.couponCode 				= '';
					this.couponMsg 					= '';
					this.totalAmountAfterDiscount 	= 0.00;
					this.shippingAmount 			= 0.00;
					this.grandTotal 				= 0.00;					
					this.loaderMessage				= "Your cart is empty!";
				}
				//this.track.trackRemoveItemFromCart(this.myCart['cart'][1]);
				if( this.inputData.optionStatus == undefined ){ this.inputData.optionStatus = 2; }
				localStorage.setItem('trackingData',JSON.stringify(res.data));
				this.track.trackCart();
			},
			(err: HttpErrorResponse) => {
				if(err.error instanceof Error){
				  console.log('Client Error: '+err.error.message);
				}else{
				  console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
				}
			}
		);
		
		return true;
	}

	initLoginForm(usr, pwd, rqd){
		if( rqd ){
			this.loginForm = new FormGroup ({
				username: new FormControl(usr, this.usernameValidator),
				password: new FormControl(pwd, Validators.compose([Validators.required]) )
			});
		}else{
			this.loginForm = new FormGroup ({
				username: new FormControl(usr, this.usernameValidator),
				password: new FormControl(pwd)
			});
		}
	}

  usernameValidator(control){
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
		let formAction:number   = 1;
		let cont:any 			= this.loginForm.controls;
		if( cont.username.invalid ){
			cont.username.markAsDirty();formAction	= 0;
		}
		
		if( formData.username == "" ){
			cont.username.markAsDirty();formAction	= 0;
		}
		
		if( (this.isStep == 2) && (formData.username != "") ){
			if( formData.password == '' ){
				formAction	= 0;
				this.resObj.otpMessage = 'Please enter OTP!';
				this.resObj.otpClass = 'text-danger';
			}
		}
		
		//check already email id logged
		if( (this.isEmail == 1) && (formData.username != "") && ( this.auth.getEmail() != "") ){
			if( formData.username == this.auth.getEmail() ){
				formAction	= 0;
				this.resObj.message = 'Sorry, You are already logged with this email id!';
				this.resObj.class = 'text-danger';
			}
		}
		
		//check already email id logged
		if( (this.isEmail == 2) && (formData.username != "") && ( this.auth.getMobile() != "") ){
			if( formData.username == this.auth.getMobile() ){
				formAction	= 0;
				this.resObj.message = 'Sorry, You are already logged with this mobile number!';
				this.resObj.class = 'text-danger';
			}
		}
		
		if( formAction ){
			if(this.isStep == 2){
				this.resObj.otpMessage = 'Wait...';
				this.resObj.otpClass = 'text-warning';
			}else{
				this.resObj.message = 'Wait...';
				this.resObj.class = 'text-warning';
			}
			let productId = localStorage.getItem('productId');
			if( productId !== null ){
				formData.productId = productId;
			}
			formData.isEmail = this.isEmail;
			formData.isStep = this.isStep;
			formData.currentUserId = this.userId;
			this.auth.signIn(formData).subscribe(
				(res)=> {
					if(res.status){
						if(this.isStep == 2){
							localStorage.setItem('usduser', JSON.stringify(res.data));
							this.hideAccountModal.nativeElement.click();
							this.isEmail 	= 0;
							this.isStep 	= 1;
							this.resObj 		= {};
							this.ngOnInit();
						}else{
							let str = ( this.isEmail == 1 ) ? 'email id':'mobile number';
							this.resObj.message = 'We have sent OTP on entered '+str+' "'+formData.username+'"';
							this.resObj.class = '';
							this.initLoginForm(formData.username,"",1);
							this.isStep = 2;
							formAction	= 0;
						}
					}else{
						if(this.isStep == 2){
							this.resObj.otpMessage = res.message;
							this.resObj.otpClass = 'text-danger';
						}else{
							this.resObj.class = 'text-danger';
							this.resObj.message = res.message;
						}
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
	}
  
	getBack(){
		this.isStep = 1;
		this.resObj.message = '';
		this.resObj.class = '';
		this.resObj.otpMessage = '';
		this.resObj.otpClass = '';
	}

	resendOtp(){
		this.resObj.otpMessage = 'Wait...';
		this.resObj.otpClass = 'text-warning';
		this.myFormData.isStep = 1;
		this.myFormData.password = '';
		this.loginForm.controls.password.setValue('', {});
		this.auth.signIn(this.myFormData).subscribe(
			(res)=> {
				if(res.status){
					this.resObj.otpMessage = '';
					this.resObj.class = '';
					this.initLoginForm(this.myFormData.username,"",1);
					this.isStep = 2;
				}else{
					this.resObj.class = 'text-danger';
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

	ngDoCheck() {
		let newUsername = this.loginForm.controls.username.value;
		let newPassword = this.loginForm.controls.password.value;
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		let MOBILE_REGEXP = /^[1-9]{1}[0-9]{9}$/;
			if( EMAIL_REGEXP.test(newUsername) ) {
			this.isEmail = 1;
		}
		if( MOBILE_REGEXP.test(newUsername) ) {
			this.isEmail = 2;
		}
		this.loginForm.controls.username.setValue(newUsername.toLowerCase(), {});
		if (this.oldUsername != newUsername) {
			this.resObj.otpMessage = '';
			this.resObj.otpClass = '';
			this.resObj.message = '';
			this.resObj.class = '';
			this.oldUsername = newUsername;
			//console.log('currentValue = '+newUsername+', previousValue = '+this.oldUsername);
		}

		if ( this.oldOtp != newPassword ){
			this.oldOtp = newPassword;
			this.resObj.otpMessage = '';
			this.resObj.otpClass = '';
		}
	}

  addCoupon(formData){
		if( formData.inCouponCode != ""  ){
			this.inputData.couponCode = formData.inCouponCode;
			this.couponMsg				= "Loading...";
			this.getMyCart();
		}else{
			this.couponMsg = 'Please enter coupon code!';
		}
	}
  

  pbWallet(event:any, num:number){
    switch(num){
      case 1: //For vouchar 
			this.inputData.giftVoucherStatus = true; 
			this.inputData.pbPointsStatus = false;
			this.inputData.couponCode = '';
			this.optionStatus = num;
			this.inputData.optionStatus = num;
			break;
      case 2: //For pb points
			this.inputData.giftVoucherStatus = false; 
			this.inputData.pbPointsStatus = true;
			this.inputData.couponCode = '';
			this.optionStatus = num;
			this.inputData.optionStatus = num;
			break;
      case 3: //For pb cash
			this.inputData.pbCashStatus = event.target.checked; 
			break;
      case 4: //For coupon code
			this.inputData.giftVoucherStatus = false; 
			this.inputData.pbPointsStatus = false;
			this.inputData.couponCode = '';
			this.optionStatus = num;
			this.inputData.optionStatus = num;
			break;
      case 5: //For prive 99 rupees
			this.inputData.pbPrive = event.target.checked; 
			break;
      default:
    }
		localStorage.setItem('usdcartInfo',JSON.stringify(this.inputData));
		this.getMyCart();
    return false;
  }

  updateTextQty(cartId:number, qty:number){
      if(qty > 0){
        let formData:any = {id:cartId,quantity:qty};
        this.store.updateCart(formData).subscribe(
          res => {
            if(res.status){
              this.getMyCart();
            }else{
              alert(res.message);
            }
          },
          (err: HttpErrorResponse) => {
            alert("Sorry, there are some app issue!");
          }
        );
      }else{
        alert("Sorry, Please enter a valid number!");
      }
  }

  confirmRemoveItemFromCartDialog(cartId){
    this.cartId = cartId;
    this.confimMsg = 'Are you sure, you want to delete this product?';
    return true;
  }

  removeItemFromCart(){
      this.confimMsg = 'Waiting ...';
      this.store.removeCart(this.cartId).subscribe(
        res => {
          if( res.status ){
            this.hideDeleteModal.nativeElement.click();
			for(let i=0; i < this.myCart['cart'].length; i++){
				if( this.myCart['cart'][i]['cart_id'] == this.cartId ){
					let itemObj:any = this.myCart['cart'][i];
					this.track.trackRemoveItemFromCart(itemObj);
					break;
				}
			}
            this.getMyCart();
          }else{
            this.confimMsg = res.message;
          }
        },
        (err: HttpErrorResponse) => {
          alert("Sorry, there are some app issue!");
        }
      );
  }

  clearDialog(){
    this.cartId=0;
    this.confimMsg='';
    return false;
  }

  storeCheckout(){
    localStorage.setItem('usdcartInfo',JSON.stringify(this.inputData));
    this.router.navigate(['/checkout/onepage']);
  }

}
