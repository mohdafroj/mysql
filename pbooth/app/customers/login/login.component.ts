import { Component, OnInit, DoCheck } 			from '@angular/core';
import { Location } 							from '@angular/common';
import { DomSanitizer, SafeResourceUrl} 		from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } 	from '@angular/forms';
import { Router, ActivatedRoute,Params } 		from '@angular/router';
import { HttpParams, HttpErrorResponse } 		from '@angular/common/http';
import { Myconfig } 							from './../../_services/pb/myconfig';
import { CustomerService } 						from './../../_services/pb/customer.service';
import { SeoService } 	   						from './../../_services/seo.service';

@Component({
  selector: 'app-login',
  //providers:[Myconfig,AuthService],
  templateUrl: './login.component.html',
  styleUrls: [
		'./../../../assets/css/login_register.css',
		'./login.component.css'
	]
})
export class LoginComponent implements OnInit {
	loginForm:FormGroup;
	myFormData:any;
	oldUsername:any;
	oldOtp:any;
	isEmail:number 	= 0;
	isStep:number 	= 1;
	resObj:any 		= {};
	requestLoginBy: number	= 0;
	bgImage:string 	= 'assets/images/pop-bg.jpg';
	constructor(
		private seo: SeoService, 
		private sanitizer:DomSanitizer, 
		private loc:Location, 
		private router: Router, 
		private route: ActivatedRoute, 
		private config:Myconfig, 
		private customer: CustomerService) 
	{
		this.bgImage = (window.innerWidth < 768) ? 'assets/images/pop-bg-mobile.jpg':'assets/images/pop-bg.jpg';
	}

	ngOnInit() { 
		this.config.scrollToTop();
		this.initForm("", "", 0);
		this.seo.ogMetaTag('Customer Login', 'Customer Login page description');
		this.signInGet();
	}
	initForm(usr, pwd, rqd){
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

	signInGet() {
		this.customer.signInGet().subscribe(
			(res)=> {
				this.requestLoginBy = res.data.requestLoginBy;
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
	
	customerLogin(formData){
		this.myFormData		= formData;
		let formAction:number   = 1;
		let cont:any 			= this.loginForm.controls;		
		let newUsername = formData.username;
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		let MOBILE_REGEXP = /^[1-9]{1}[0-9]{9}$/;
		switch ( this.requestLoginBy ) {
			case 1:
				if( !EMAIL_REGEXP.test(newUsername) ) {
					cont.username.markAsDirty();
					//cont.username.markAsStatus(false); console.log(this.loginForm);
					formAction	= 0;
				}
				break;
			case 2:
				if( !MOBILE_REGEXP.test(newUsername) ) {
					cont.username.markAsDirty();formAction	= 0;
				}
				break;
			default:
		}
		if( cont.username.invalid ){
			cont.username.markAsDirty();formAction	= 0;
		}
		
		if( formData.username == "" ){
			cont.username.markAsDirty();formAction	= 0;
		}
		
		if( (this.isStep == 2) && (formData.username != "") ){
			if( formData.password == '' ){
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
				this.resObj.class = 'text-warning';
			}
			let productId = localStorage.getItem('productId');
			if( productId !== null ){
				formData.productId = productId;
			}
			formData.isEmail = this.isEmail;
			formData.isStep = this.isStep;
			//this.route.queryParams.subscribe((params: Params) => {login.productId = params['product-id']; });
			this.customer.signIn(formData).subscribe(
				(res)=> {
					if(res.status){
						if(this.isStep == 2){
							localStorage.setItem('user', JSON.stringify(res.data));
							if( productId !== null ){
								this.router.navigate(['/checkout/cart/']);
							}else{
								this.loc.back();
							}
						}else{
							let str = ( this.isEmail == 1 ) ? 'email id':'mobile number';
							this.resObj.message = 'We have sent OTP on entered '+str+' "'+formData.username+'"';
							this.resObj.class = '';
							this.initForm(formData.username,"",1);
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
		} else {
			if ( newUsername != "" ){
			this.resObj.class = 'text-danger';
				switch ( this.requestLoginBy ) {
					case 1:
						this.resObj.message = 'Please enter valid email id!';
						break;
					case 2:
						this.resObj.message = 'Please enter valid mobile number!';
						break;
					default:
						this.resObj.message = 'Please enter valid email id or mobile number!';
				}
			}
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
		this.customer.signIn(this.myFormData).subscribe(
			(res)=> {
				if(res.status){
					this.resObj.otpMessage = '';
					this.resObj.class = '';
					this.initForm(this.myFormData.username,"",1);
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

	checkEmailOrPassword(e) {
		let newUsername = this.loginForm.controls.username.value;
		let newPassword = this.loginForm.controls.password.value;
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		let MOBILE_REGEXP = /^[1-9]{1}[0-9]{9}$/;
		if ( this.requestLoginBy != 0 ) {
			this.isEmail = this.requestLoginBy;
		} else {
			if( EMAIL_REGEXP.test(newUsername) ) {
				this.isEmail = 1;
			}
			if( MOBILE_REGEXP.test(newUsername) ) {
				this.isEmail = 2;
			}
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
}
