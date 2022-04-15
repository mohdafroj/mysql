import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../../_services/pb/myconfig';
import { PagesService } from './../../../_services/pb/pages.service';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-contact',
  templateUrl: './contact.component.html',
  styleUrls: [
		'./../../../../assets/css/static_page.css',
		'./contact.component.css'
	]
})
export class ContactComponent implements OnInit {
  rForm:FormGroup;
  msg:string			= '';
  constructor(private config:Myconfig,private pages: PagesService, private seo:SeoService ) { 
  }

  ngOnInit() {
	this.config.scrollToTop();
    this.rForm = new FormGroup ({
      firstname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
      lastname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
      email: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
      mobile: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.MOBILE_REGEXP)]) ),
      comment: new FormControl("", Validators.compose([Validators.required]) )
    });
	this.seo.ogMetaTag("Contact Us", "Contact Us description");
	        
  }

  contactUs(formData){
    this.msg = 'Wait...';
    this.pages.contactUs(formData).subscribe(
      res => {
        if(res.status){
			this.rForm = new FormGroup ({
				firstname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
				lastname: new FormControl("", Validators.compose([Validators.required,Validators.pattern(this.config.ALPHA_SPACE_REGEXP),Validators.minLength(3)]) ),
				email: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) ),
				mobile: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.MOBILE_REGEXP)]) ),
				comment: new FormControl("", Validators.compose([Validators.required]) )
			});
        }
        this.msg = res.message;
      },
      (err: HttpErrorResponse) => {
        if(err.error instanceof Error){
          this.msg = 'Client error: '+err.error.message;
        }else{
          this.msg = 'Server error: '+JSON.stringify(err.error);
        }
      }
    );
  }
  
  upperToLower(event){
	  (<FormControl>this.rForm.controls['email']).setValue(event.target.value.toLowerCase(), {});
  }
  
}
