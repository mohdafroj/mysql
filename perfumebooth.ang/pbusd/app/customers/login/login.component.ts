import { Component, OnInit, DoCheck } 			from '@angular/core';
import { Location } 							from '@angular/common';
import { DomSanitizer, SafeResourceUrl} 		from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } 	from '@angular/forms';
import { Router, ActivatedRoute,Params } 		from '@angular/router';
import { HttpParams, HttpErrorResponse } 		from '@angular/common/http';
import { Myconfig } 							from './../../_services/pb/myconfig';
import { CustomerService } 						from '../../_services/pb/customer.service';

@Component({
  selector: 'app-login',
  //providers:[Myconfig,AuthService],
  templateUrl: './login.component.html',
  styleUrls: [
		'./../../../assets/css/login_register.css',
		'./login.component.css'
	]
})
export class LoginComponent implements OnInit,DoCheck {
	loginForm:FormGroup;
	myFormData: any;
	oldUsername: any;
	oldOtp: any;
	isStep:number 	= 1;
	errorObj:any = {};
	resObj:any 		= {};
	bgImage:string 	= 'assets/images/pop-bg.jpg';
	constructor(private sanitizer:DomSanitizer, private loc:Location, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
		this.bgImage = (window.innerWidth < 768) ? 'assets/images/pop-bg-mobile.jpg':'assets/images/pop-bg.jpg';
	}

	ngOnInit() { 
		this.config.scrollToTop();
		//this.initForm("", "", 0);
		this.loginForm = new FormGroup ({
			username: new FormControl("", this.usernameValidator),
			password: new FormControl("", Validators.compose([Validators.required]) )
		});
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
		if( !EMAIL_REGEXP.test(control.value) ) {
			return {'username': true};
		}
	}
	
	getFixedErrors(formData){ //console.log(formData);
		if (  this.loginForm.controls.password.invalid ){
			this.loginForm.controls.password.markAsDirty();
			this.errorObj.password = 'Please enter your password!';
		}else{
			this.errorObj.password = '';
		}
		if ( formData.username == "" || this.loginForm.controls.username.invalid ){
			this.loginForm.controls.username.markAsDirty();
			this.errorObj.username = 'Please enter valid emaid id!';
		}else{
			this.errorObj.username = '';
		}
	}
	
	customerLogin(formData){
		//this.errorObj = {};
		this.myFormData		= formData;
		let formAction= 1;
		if (  this.loginForm.controls.password.invalid ){
			this.loginForm.controls.password.markAsDirty();
			this.errorObj.password = 'Please enter your password!';
			formAction = 0;
		}		
		if ( this.loginForm.controls.username.invalid ){
			this.loginForm.controls.username.markAsDirty();
			this.errorObj.username = 'Please enter valid email id!';
			formAction = 0;
		}
		
		if( formAction == 1 ){
			this.resObj.message = 'Wait...';
			this.resObj.class = 'text-warning';
			let productId = localStorage.getItem('productId');
			if( productId !== null ){
				formData.productId = productId;
			}
			//this.route.queryParams.subscribe((params: Params) => {login.productId = params['product-id']; });
			this.auth.signIn(formData).subscribe(
				(res)=> {
					if(res.status){
						localStorage.setItem('usduser', JSON.stringify(res.data));
						if( productId !== null ){
							this.router.navigate(['/checkout/cart/']);
						}else{
							this.loc.back();
						}
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
		this.myFormData.password = '';
		this.loginForm.controls.password.setValue('', {});
		this.auth.signIn(this.myFormData).subscribe(
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
		let newPassword = this.loginForm.controls.password.value;
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
