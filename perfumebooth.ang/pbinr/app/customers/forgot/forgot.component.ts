import { Component, Input, OnInit } from '@angular/core';
//import { Title } from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Myconfig } 			from './../../_services/pb/myconfig';
import { CustomerService } 		from './../../_services/pb/customer.service';
import { SeoService } 	   		from './../../_services/seo.service';

@Component({
  selector: 'app-forgot',
  templateUrl: './forgot.component.html',
  styleUrls: [
		'./../../../assets/css/login_register.css',
		'./forgot.component.css'
	]
})
export class ForgotComponent implements OnInit {
  rForm:FormGroup;
  forgetMessage:string	='';
  forgetStatus:boolean	=false;
  constructor(private seo: SeoService, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
  }
  ngOnInit() {
  	window.scrollTo(0, 0);
    this.rForm = new FormGroup ({
		username: new FormControl("",Validators.compose([Validators.required,Validators.pattern(this.config.EMAIL_REGEXP)]))
    });
	this.seo.ogMetaTag('Password Foget', 'Password Forget page description');

  }

  forgotPassword(formData) {
    this.forgetMessage = 'Wait...';
    this.forgetStatus = false;
    /*
    this.auth.forgotPassword(formData).subscribe(
      res => {
        this.forgetMessage = res.message;
        if (res.status) {
			this.rForm = new FormGroup ({
				username: new FormControl("")
			});
			this.forgetStatus = true;
        }
      },
      (err: HttpErrorResponse) => {
        if (err.error instanceof Error) {
          this.forgetMessage = 'Client error: ' + err.error.message;
        } else {
          this.forgetMessage = 'Server error: ' + JSON.stringify(err.error);
        }
      }
    );*/
  }
  
  upperToLower(event){
	  (<FormControl>this.rForm.controls['username']).setValue(event.target.value.toLowerCase(), {});
  }

}
