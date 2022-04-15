import { Component, Input, OnInit, ViewEncapsulation } 	from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { Location } 								from '@angular/common';
import { FormGroup, FormControl, Validators } 		from '@angular/forms';
import { Router, ActivatedRoute, Params } 			from '@angular/router';
import { HttpParams, HttpErrorResponse } 			from '@angular/common/http';
import { Myconfig } 								from './../../_services/pb/myconfig';
import { CustomerService } 							from '../../_services/pb/customer.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: '[pc-register]',
  templateUrl: './register.component.html',
  styleUrls: [
		'./register.component.css'
	],
  encapsulation: ViewEncapsulation.None
})
export class RegisterComponent implements OnInit {
	registerForm:FormGroup;
	myFormData:any;
	isStep = 1;
	tokenForAccount = '';
	resObj = {};
	oldUsername = '';
	oldEmail = '';
	oldMobile = '';
	oldOtp = '';
	referMessage = '';
	sanitizer;
	serverRequest:boolean = true;
	constructor (
		private toastr: ToastrService,
		private loc: Location, 
		private router: Router, 
		private route: ActivatedRoute, 
		private config: Myconfig, 
		private customer: CustomerService,
		private sanitize: DomSanitizer,
	) {
		this.sanitizer = sanitize;
	}

	ngOnInit () {
		this.config.scrollToTop();
		this.registerForm = new FormGroup ({
			username: new FormControl("", Validators.compose([Validators.required]) ),
			email: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
			mobile: new FormControl("", Validators.compose([Validators.required, Validators.minLength(10)]) ),
			otp: new FormControl("", Validators.compose([Validators.minLength(6)]) )
		});
		this.checkReferalCustomer();
	}

	checkReferalCustomer () {
		let formData = {};
		this.route.queryParams.subscribe((params: Params) => {
			formData['referCode'] = params['refer'];
		});
		this.customer.referCustomer(formData).subscribe(
			res => {
				this.referMessage = res.message;
			},
			(err: HttpErrorResponse) => {
			}
		);
	}
	customerRegister( formData ) { 
		formData.isStep = this.isStep;
		this.myFormData	= formData;
		let formAction:number = 1;
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
			
			if( this.serverRequest ){
				this.serverRequest = false;
				let productId = localStorage.getItem('productId');
				if( productId != null ){
					formData.productId = productId;
				}				
				this.customer.signUp(formData).subscribe(
					res => {
						//console.log(res);
						this.serverRequest = true;
						if ( res.status ) {
							if ( this.isStep == 2 ) {
								this.customer.setAccount(res.data);
								localStorage.removeItem('productId');
								if( productId != null ) {
									this.router.navigate(['/checkout/cart']);
								} else {
									this.router.navigate(['/customer/profile']);
								}
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
						this.serverRequest = true;
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

	getBack(){
		this.isStep = 1;
		this.resObj['message'] = '';
		this.resObj['textClass'] = '';
		this.resObj['otpMessage'] = '';
		this.resObj['otpClass'] = '';
	}
  
	resendOtpRegister () {
		this.resObj['otpMessage'] = 'Wait...';
		this.resObj['otpClass'] = 'text-warning';
		this.myFormData.isStep = 1;
		this.myFormData.otp = '';
		this.registerForm.controls.otp.setValue('', {});
		if( this.serverRequest ){
			this.serverRequest = false;
			this.customer.signUp(this.myFormData).subscribe(
				(res)=> {
					this.serverRequest = true;
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
					this.serverRequest = true;
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
		let newUsername = this.registerForm.controls.username.value;
		let newEmail = this.registerForm.controls.email.value;
		let newMobile = this.registerForm.controls.mobile.value;
		let newOtp = this.registerForm.controls.otp.value;
		this.registerForm.controls.email.setValue(newEmail.toLowerCase(), {});

		if ( (this.oldUsername != newUsername) || (this.oldEmail != newEmail) || (this.oldMobile != newMobile) ) {
			this.oldUsername = newUsername;
			this.oldEmail = newEmail;
			this.oldMobile = newMobile;
			this.resObj = {message:'', textClass:'', otpMessage:'', otpClass:''};
		}

		if ( this.oldOtp != newOtp ){
			this.oldOtp = newOtp;
			this.resObj['otpClass'] = this.resObj['otpMessage'] = '';
		}
	}

}
