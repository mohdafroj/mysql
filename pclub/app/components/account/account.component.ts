import { Component, OnInit, ViewEncapsulation, ElementRef, Output, EventEmitter } from '@angular/core';
import { Location } 							from '@angular/common';
import { DomSanitizer, SafeResourceUrl} 		from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } 	from '@angular/forms';
import { Router, ActivatedRoute,Params } 		from '@angular/router';
import { HttpParams, HttpErrorResponse } 		from '@angular/common/http';
import { Myconfig } 							from './../../_services/pb/myconfig';
import { CustomerService } 						from './../../_services/pb/customer.service';
import { ProductsService } 						from './../../_services/pb/products.service';
import { ToastrService } from 'ngx-toastr';


@Component({
  selector: '.account',
  templateUrl: './account.component.html',
  styleUrls: ['./account.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class AccountComponent implements OnInit {
	@Output() loggedUser = new EventEmitter<number>();
	
	userId = 0;
	formType = false;
	myFormData: any;
	loginForm: FormGroup;
	registerForm: FormGroup;
	oldUsername:any;
	oldOtp:any;
	isEmail:number 	= 0;
	isStep:number 	= 1;
	resObj:any 		= {};
	serverRequest: boolean = true;
	tokenForAccount:string 	= '';
	constructor (
		private toastr: ToastrService,
		private product: ProductsService,
		private elem: ElementRef,
		private sanitizer:DomSanitizer,
		private loc:Location,
		private router: Router,
		private route: ActivatedRoute,
		private config:Myconfig,
		private customer: CustomerService
	) { 
	
	}

	ngOnInit () {
		this.userId = this.customer.getId();
		this.loginForm = new FormGroup ({
			username: new FormControl("", this.usernameValidator),
			otp: new FormControl("", Validators.compose([Validators.required]) )
		});
		
		this.registerForm = new FormGroup ({
			username: new FormControl("", Validators.compose([Validators.required]) ),
			email: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
			mobile: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.MOBILE_REGEXP)]) ),
			otp: new FormControl("")
		});
	}
	
	checkUserId () {
		this.loggedUser.emit(123);
	}
	
	changeFormType () {
		this.formType = (this.formType == false) ? true : false;
		this.getBack();
	}
	
	usernameValidator (control) {
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		let MOBILE_REGEXP = /^[1-9]{1}[0-9]{9}$/;
		if( !EMAIL_REGEXP.test(control.value) ) {
			if (!MOBILE_REGEXP.test(control.value)) {
				return {'username': true};
			}
		}
	}

	initLoginForm (usr, pwd, rqd) {
		if( rqd ){
			this.loginForm = new FormGroup ({
				username: new FormControl(usr, this.usernameValidator),
				otp: new FormControl(pwd, Validators.compose([Validators.required]) )
			});
		}else{
			this.loginForm = new FormGroup ({
				username: new FormControl(usr, this.usernameValidator),
				otp: new FormControl(pwd)
			});
		}
	}
	
	checkLogin() {
		let newUsername = this.loginForm.controls.username.value;
		let newPassword = this.loginForm.controls.otp.value;
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		let MOBILE_REGEXP = /^[1-9]{1}[0-9]{9}$/;
		if ( EMAIL_REGEXP.test(newUsername) ) {
			this.isEmail = 1;
		} else if( MOBILE_REGEXP.test(newUsername) ) {
			this.isEmail = 2;
		} else {
			this.isEmail = 0;
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
	
	customerLogin(formData){
		let formAction:number   = 1;
		let cont:any 			= this.loginForm.controls;
		if( cont.username.invalid ){
			cont.username.markAsDirty();formAction	= 0;
		}
		
		if( formData.username == "" ){
			cont.username.markAsDirty();formAction	= 0;
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
				this.resObj.class = 'text-warning';
			}
			formData.isEmail = this.isEmail;
			formData.isStep = this.isStep;
			if ( this.serverRequest ) {
				this.serverRequest = false;
				this.customer.signIn(formData).subscribe(
					(res)=> {
						this.serverRequest = true;
						if(res.status){
							if(this.isStep == 2){
								this.customer.setAccount(res.data);
								this.elem.nativeElement.querySelector(".close").click();
								this.loggedUser.emit(res.data.id);
							}else{
								let str = ( this.isEmail == 1 ) ? 'email id':'mobile number';
								this.resObj.message = 'We have sent OTP on entered '+str+' "'+formData.username+'"';
								this.resObj.class = '';
								this.initLoginForm(formData.username, "", 1);
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
						this.serverRequest = true;
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
		}	
	}

	resendOtpLogin () {
		this.resObj.otpMessage = 'Wait...';
		this.resObj.otpClass = 'text-warning';
		this.loginForm.controls.otp.setValue('', {});
		let name = this.loginForm.controls.username.value;
		let formData = {username: name, otp: '', isStep: 1, isEmail: this.isEmail};
		if ( this.serverRequest ) {
			this.serverRequest = false;
			this.customer.signIn(formData).subscribe(
				(res)=> {
					this.serverRequest = true;
					if(res.status){
						this.resObj.otpMessage = '';
						this.resObj.class = '';
						this.initLoginForm(formData.username, "", 1);
						this.isStep = 2;
					}else{
						this.resObj.class = 'text-danger';
						this.resObj.message = res.message;
					}
				},
				(err: HttpErrorResponse) => {
					this.serverRequest = true;
					this.resObj.otpClass = 'text-danger resend_otp';
					if(err.error instanceof Error){
						this.resObj.message = 'Client error: '+err.error.message;
					}else{
						this.resObj.message = 'Server error: There are some server issue!';
					}
				}
			);
		} else {
			this.toastr.warning("Please wait ...");
		}
	}
	
	getBack(){
		this.isStep = 1;
		this.resObj = {message: '', class: '', otpMessage: '', otpClass: ''};
	}
	
	// For registration
	customerRegister( formData ) {
		formData.isStep = this.isStep;
		this.myFormData		= formData;
		let formAction:number = 1;
		let cont:any = this.registerForm.controls;
		if( cont.username.invalid ){
			cont.username.markAsDirty();formAction	= 0;
		}
		if( cont.mobile.invalid ){
			cont.mobile.markAsDirty();formAction	= 0;
		}
		if( cont.email.invalid ){
			cont.email.markAsDirty();formAction	= 0;
		}

		if( (this.isStep == 2) && (formData.username != "") ){
			if( formData.otp == '' ){
				formAction	= 0;
				this.resObj.otpMessage = 'Please enter OTP!';
				this.resObj.otpClass = 'text-danger';
			}else{
				formData.token = this.tokenForAccount;
			}
		}
		
		if( formAction ){
			if ( this.isStep == 2 ){
				this.resObj.otpMessage = 'Wait...';
				this.resObj.otpClass = 'text-warning';
			} else {
				this.resObj.message = 'Wait...';
				this.resObj.class = 'text-warning';
			}
			this.route.queryParams.subscribe((params: Params) => {
				formData.ref = params['ref'];
			});
			
			if( this.serverRequest ){
				this.serverRequest = false;
				formData.gender = this.product.getGender();
				this.customer.signUp(formData).subscribe(
					res => {
						//console.log(res);
						this.serverRequest = true;
						if ( res.status ) {
							if ( this.isStep == 2 ) {
								this.customer.setAccount(res.data);
								this.elem.nativeElement.querySelector(".close").click();
								this.loggedUser.emit(res.data.id);
							} else {
								this.resObj.message = 'We have sent OTP on mobile number "'+formData.mobile+'" and email id "'+formData.email+'"';
								this.resObj.class = '';
								this.isStep = 2;
								this.tokenForAccount = res.data.token;
								formAction	= 0;
							}
						} else {
							if ( this.isStep == 2 ) {
								this.resObj.otpMessage = res.message;
								this.resObj.otpClass = 'text-danger';
							} else {
								this.resObj.class = 'text-danger';
								this.resObj.message = res.message;
							}
						}
					},
					(err: HttpErrorResponse) => {
						this.serverRequest = true;
						this.resObj.otpClass = 'text-danger resend_otp';
						if(err.error instanceof Error){
							this.resObj.message = 'Client error: '+err.error.message;
						}else{
							this.resObj.message = 'Server error: There are some server issue!';
						}
					}
				);
			}			
		}
		return true;
	}
	
	resendOtpRegister () {
		this.resObj.otpMessage = 'Wait...';
		this.resObj.otpClass = 'text-warning';
		this.myFormData.isStep = 1;
		this.myFormData.otp = '';
		this.registerForm.controls.otp.setValue('', {});
		if( this.serverRequest ){
			this.serverRequest = false;
			this.customer.signUp(this.myFormData).subscribe(
				(res)=> {
					this.serverRequest = true;
					if(res.status){
						this.resObj.otpMessage = '';
						this.resObj.class = '';
						this.isStep = 2;
						this.tokenForAccount = res.data.token;
					}else{
						this.resObj.class = 'text-danger';
						this.resObj.message = res.message;
					}
				},
				(err: HttpErrorResponse) => {
					this.serverRequest = true;
					this.resObj.otpClass = 'text-danger resend_otp';
					if(err.error instanceof Error){
						this.resObj.message = 'Client error: '+err.error.message;
					}else{
						this.resObj.message = 'Server error: There are some server issue!';
					}
				}
			);
		}
	}

}
