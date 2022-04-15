import { Component, Input, OnInit } 				from '@angular/core';
import { Location } 								from '@angular/common';
import { FormGroup, FormControl, Validators } 		from '@angular/forms';
import { Router, ActivatedRoute, Params } 			from '@angular/router';
import { HttpParams, HttpErrorResponse } 			from '@angular/common/http';
import { Myconfig } 								from './../../_services/pb/myconfig';
import { CustomerService } 							from './../../_services/pb/customer.service';
import { SeoService } 	   							from './../../_services/seo.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: [
		'./../../../assets/css/login_register.css',
		'./register.component.css'
	]
})
export class RegisterComponent implements OnInit {
	rForm:FormGroup;
	myFormData:any;
	oldUsername:any;
	oldEmail:any;
	oldOtp:any;
	isStep:number 	= 1;
	tokenForAccount:string 	= '';
	resObj:any 		= {};
	bgImage:string = 'assets/images/pop-bg.jpg';
	serverRequest:boolean = true;
	constructor(private seo: SeoService, private loc:Location, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
		this.bgImage = (window.innerWidth < 768) ? 'assets/images/pop-bg-mobile.jpg':'assets/images/pop-bg.jpg';
	}

	ngOnInit() {
		this.seo.ogMetaTag('Registration Page', 'Registration page description');
		this.config.scrollToTop();
		this.initForm();
	}

	initForm(){
		this.rForm = new FormGroup ({
			username: 		 new FormControl("", Validators.compose([Validators.required]) ),
			email: 			 new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
			mobile: 		 new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.MOBILE_REGEXP)]) ),
			password: 		 new FormControl("")
		});		
	}

	customerRegister(formData){
		formData.isStep = this.isStep;
		this.myFormData		= formData;
		let formAction:number = 1;
		let cont:any 			= this.rForm.controls;
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
			if( formData.password == '' ){
				formAction	= 0;
				this.resObj.otpMessage = 'Please enter OTP!';
				this.resObj.otpClass = 'text-danger';
			}else{
				formData.token = this.tokenForAccount;
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
			this.route.queryParams.subscribe((params: Params) => {
				formData.ref = params['ref'];
			});
			let productId = localStorage.getItem('productId');
			if( productId !== null ){
				formData.productId = productId;
			}
			
			if( this.serverRequest ){
				this.serverRequest = false;
				this.auth.signUp(formData).subscribe(
					res => {
						//console.log(res);
						this.serverRequest = true;
						if(res.status){
							if( this.isStep == 2 ){
								localStorage.setItem('user', JSON.stringify(res.data));
								if( productId !== null ){
									this.router.navigate(['/checkout/cart/']);
								}else{
									this.router.navigate(['/customer/profile']);
								}
							}else{
								this.resObj.message = res.message; //'We have sent OTP on mobile number "'+formData.mobile+'" and email id "'+formData.email+'"';
								this.resObj.class = '';
								this.isStep = 2;
								this.tokenForAccount = res.data.token;
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
							this.resObj.message = 'Server error: '+JSON.stringify(err.error);
						}
					}
				);
			}			
		}
		return true;
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
		this.rForm.controls.password.setValue('', {});
		if( this.serverRequest ){
			this.serverRequest = false;
			this.auth.signUp(this.myFormData).subscribe(
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
						this.resObj.message = 'Server error: '+JSON.stringify(err.error);
					}
				}
			);
		}
	}

	checkInputFields(e) {
		let newUsername = this.rForm.controls.username.value;
		let newEmail = this.rForm.controls.email.value;
		let newPassword = this.rForm.controls.password.value;
		this.rForm.controls.email.setValue(newEmail.toLowerCase(), {});

		if ( (this.oldUsername != newUsername) || (this.oldEmail != newEmail) ) {
			this.resObj.class = this.resObj.message = this.resObj.otpClass = this.resObj.otpMessage = '';
			this.oldUsername = newUsername;
			this.oldEmail = newEmail;
			//console.log('currentValue = '+newUsername+', previousValue = '+this.oldUsername);
		}

		if ( this.oldOtp != newPassword ){
			this.oldOtp = newPassword;
			this.resObj.otpClass = this.resObj.otpMessage = '';
		}
	}

}
