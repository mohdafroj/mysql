import { Component, OnInit, DoCheck, ViewEncapsulation } 			from '@angular/core';
import { Location } 							from '@angular/common';
import { DomSanitizer, SafeResourceUrl} 		from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } 	from '@angular/forms';
import { Router, ActivatedRoute,Params } 		from '@angular/router';
import { HttpParams, HttpErrorResponse } 		from '@angular/common/http';
import { Myconfig } 							from './../../_services/pb/myconfig';
import { CustomerService } 						from '../../_services/pb/customer.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: '[pc-login]',
  templateUrl: './login.component.html',
  styleUrls: [
		'./login.component.css'
	],
	encapsulation: ViewEncapsulation.None
})
export class LoginComponent implements OnInit,DoCheck {
	loginForm:FormGroup;
	myFormData: any;
	oldUsername: any;
	oldOtp: any;
	isEmail:number 	= 0;
	isStep:number 	= 1;
	resObj:any 		= {};
	serverRequest: boolean = true;
	otpSelectorMessage = '';
	otpSelectorStatus:number = 0;
	constructor( 
		private toastr: ToastrService,
		private sanitizer: DomSanitizer, 
		private loc: Location, 
		private router: Router, 
		private route: ActivatedRoute, 
		private config:Myconfig, 
		private customer: CustomerService
	) {
	}

	ngOnInit() { 
		this.config.scrollToTop();
		this.otpSelectorMessage = this.config.otpSelectorMessage;
		this.otpSelectorStatus = this.config.otpSelectorStatus;
		this.otpSelector();
		this.loginForm = new FormGroup ({
			username: new FormControl(""),
			otp: new FormControl("")
		});
	}

	otpSelector() {
		this.customer.otpSelector().subscribe(
			(res)=> {
				this.otpSelectorMessage = res.message;
				this.otpSelectorStatus = res.data.otpSelectorStatus;
			},
			(err: HttpErrorResponse) => {
				
			}
		);
	}
		
	customerLogin(formData){
		this.myFormData		= formData;
		let formAction= 1;
		let emailReg = this.config.EMAIL_REGEXP;
		let mobileReg = this.config.MOBILE_REGEXP;
		if ( this.loginForm.controls.username.invalid || (formData.username == '') ){
			this.loginForm.controls.username.markAsDirty();
			switch (this.otpSelectorStatus) {
				case 1:
					if( !emailReg.test(formData.username) ) {
						this.resObj.message = 'Please enter valid email id!';
					}
					break;
				case 2:
					if( !mobileReg.test(formData.username) ) {
						this.resObj.message = 'Please enter valid mobile number!';
					}
					break;
				default:
					if ( this.isEmail == 1 ) {
						this.resObj.message = 'Please enter valid email id!';
					} else {
						this.resObj.message = 'Please enter valid mobile number!';
					}
			}
			formAction = 0;
		}
		if ( formAction ) {
			switch (this.otpSelectorStatus) {
				case 1:
					if( !emailReg.test(formData.username) ) {
						this.resObj.message = 'Please enter valid email id!';
						formAction = 0;
					}
					break;
				case 2:
					if( !mobileReg.test(formData.username) ) {
						this.resObj.message = 'Please enter valid mobile number!';
						formAction = 0;
					}
					break;
				default:
					if ( this.isEmail == 1 ) {
						this.resObj.message = 'Please enter valid email id!';
					} else {
						this.resObj.message = 'Please enter valid mobile number!';
					}
			}
		}		
		if( (this.isStep == 2) && (formData.username != "") ){
			if( formData.otp == '' ){
				formAction	= 0;
				this.resObj.otpMessage = 'Please enter valid OTP!';
				this.resObj.otpClass = 'text-danger';
			}
		} //console.log(this.resObj);
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
			let productId = localStorage.getItem('productId');
			if( productId != null ){
				formData.productId = productId;
			}
			if ( this.serverRequest ) {
				this.serverRequest = false;
				this.customer.signIn(formData).subscribe(
					(res)=> {
						this.serverRequest = true;
						if(res.status){
							if(this.isStep == 2){
								this.customer.setAccount(res.data);
								localStorage.removeItem('productId');
								if( productId != null ){
									this.router.navigate(['/checkout/cart/']);
								}else{
									this.router.navigate(['/customer/profile']);
								}
							}else{
								let str = ( this.isEmail == 1 ) ? 'email id':'mobile number';
								this.resObj.message = 'We have sent OTP on entered '+str+' "'+formData.username+'"';
								this.resObj.class = '';
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
		} else {
			this.resObj.class = 'text-danger';
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
		this.myFormData.otp = '';
		this.loginForm.controls.otp.setValue('', {});
		this.customer.signIn(this.myFormData).subscribe(
			(res)=> {
				if(res.status){
					this.resObj.otpMessage = '';
					this.resObj.class = '';
					//this.initForm(this.myFormData.username,"",1);
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
		let newPassword = this.loginForm.controls.otp.value;
		let emailReg = this.config.EMAIL_REGEXP;
		let mobileReg = this.config.MOBILE_REGEXP;
		if( emailReg.test(newUsername) ) {
			this.isEmail = 1;
		}
		if( mobileReg.test(newUsername) ) {
			this.isEmail = 2;
		}
		this.loginForm.controls.username.setValue(newUsername.toLowerCase(), {});
		if (this.oldUsername != newUsername) {
			this.resObj.otpMessage = '';
			this.resObj.otpClass = '';
			this.resObj.message = '';
			this.resObj.class = '';
			this.oldUsername = newUsername;
		}

		if ( this.oldOtp != newPassword ){
			this.oldOtp = newPassword;
			this.resObj.otpMessage = '';
			this.resObj.otpClass = '';
		}
	}
}
