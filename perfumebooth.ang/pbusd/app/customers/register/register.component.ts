import { Component, Input, OnInit } 				from '@angular/core';
import { Location } 								from '@angular/common';
import { FormGroup, FormControl, Validators } 		from '@angular/forms';
import { Router, ActivatedRoute, Params } 			from '@angular/router';
import { HttpParams, HttpErrorResponse } 			from '@angular/common/http';
import { Myconfig } 								from './../../_services/pb/myconfig';
import { CustomerService } 							from '../../_services/pb/customer.service';

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
	errorObj: any = {};
	oldEmail: any;
	oldOtp: any;
	isStep:number 	= 1;
	tokenForAccount:string 	= '';
	resObj:any 		= {};
	bgImage:string = 'assets/images/pop-bg.jpg';
	serverRequest:boolean = true;
	constructor(private loc:Location, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
		this.bgImage = (window.innerWidth < 768) ? 'assets/images/pop-bg-mobile.jpg':'assets/images/pop-bg.jpg';
	}

	ngOnInit() {
		this.config.scrollToTop();
		this.initForm();
	}

	initForm(){
		this.rForm = new FormGroup ({
			username: 		 new FormControl("", Validators.compose([Validators.required]) ),
			email: 			 new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
			password: 		 new FormControl("", Validators.compose([Validators.required, Validators.minLength(6)]) ),
			confirmPassword: new FormControl("", Validators.compose([Validators.required]) )
		});		
	}
	getFixedErrors(formData){ //console.log(formData);
		if( formData.confirmPassword == "" || (formData.confirmPassword != formData.password) ){
			this.rForm.controls.confirmPassword.markAsDirty();
			this.errorObj.confirmPassword =  'Confirm password should not match!';
		}else{
			this.errorObj.confirmPassword =  '';
		}
		if (  this.rForm.controls.password.invalid ){
			this.rForm.controls.password.markAsDirty();
			this.errorObj.password = 'Please enter at least 6 char long password!';
		}else{
			this.errorObj.password = '';
		}
		if ( (formData.email == "") || this.rForm.controls.email.invalid ){
			this.errorObj.email = 'Please enter a valid email id!';
			this.rForm.controls.email.markAsDirty();
		}else{
			this.errorObj.email = '';
		}
		if ( formData.username == "" || this.rForm.controls.username.invalid ){
			this.rForm.controls.username.markAsDirty();
			this.errorObj.username = 'Please enter user name!';
		}else{
			this.errorObj.username = '';
		}
	}
	customerRegister(formData){
		//this.errorObj = {username: '', email: '', password: '', confirmPassword: ''};
		this.myFormData		= formData;
		let formAction = 1;
		if( formData.confirmPassword == "" || (formData.confirmPassword != formData.password) ){
			this.rForm.controls.confirmPassword.markAsDirty();
			this.errorObj.confirmPassword =  'Confirm password should not match!';
			formAction = 0;
		}
		if (  this.rForm.controls.password.invalid ){
			this.rForm.controls.password.markAsDirty();
			this.errorObj.password = 'Please enter at least 6 char long password!';
			formAction = 0;
		}
		if ( (formData.email == "") || this.rForm.controls.email.invalid ){
			this.rForm.controls.email.markAsDirty();
			this.errorObj.email = 'Please enter a valid email id!';
			formAction = 0;
		}
		if ( this.rForm.controls.username.invalid ){
			this.rForm.controls.username.markAsDirty();
			this.errorObj.username = 'Please enter user name!';
			formAction = 0;
		}
		
		if( formAction == 1 ){
			this.resObj.message = 'Wait...';
			this.resObj.class = 'text-warning';
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
							localStorage.setItem('usduser', JSON.stringify(res.data));
							if( productId !== null ){
								this.router.navigate(['/checkout/cart/']);
							}else{
								this.router.navigate(['/customer/profile']);
							}
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
		}else{
			this.resObj.class = 'text-danger';
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
			this.serverRequest = true;
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

	ngDoCheck() {
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
