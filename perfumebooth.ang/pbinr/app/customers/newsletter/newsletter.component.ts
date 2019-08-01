import { Component, Input, OnInit } from '@angular/core';
//import { Title } from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Myconfig } 		from './../../_services/pb/myconfig';
import { CustomerService } 	from './../../_services/pb/customer.service';
import { SeoService } 	   	from './../../_services/seo.service';

@Component({
  selector: 'app-newsletter',
  templateUrl: './newsletter.component.html',
  styleUrls: [
		'./../../../assets/css/user-dashboard.css',
		'./newsletter.component.css'
	]
})
export class NewsletterComponent implements OnInit {
  rForm:FormGroup;
  msg:string;
  response:any;
  constructor(private seo: SeoService, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
  }
  ngOnInit() {
    this.rForm = new FormGroup ({ newsletter: new FormControl(false) });
    this.response = {
      firstname:'',
      lastname:'',
      email:'',
      gender:'',
      dob:'',
      profession:'',
      address:'',
      city:'',
      pincode:'',
      mobile:'',
      newsletter:false,
      image:'',
      location_id:0,
      created:'',
      modified:'',
      áddresses:[],
      locations:[]
    }
    this.auth.getProfile().subscribe(
      res => {
        if(res.status){
          this.response = res.data;
        }else{
          console.log(res.message);
        }
        this.rForm = new FormGroup ({ newsletter: new FormControl(this.response.newsletter) });
      },
      (err: HttpErrorResponse) => {
        console.log("Server Isse!");
      }
    );
	this.seo.ogMetaTag('Newsletter Page', 'Newsletter page description');

  }

  updateNewsletterStatus(formData) {
    this.msg = 'Wait...'; console.log(formData);
    this.auth.updateNewsletterStatus(formData).subscribe(
      res => {
        this.msg = res.message;
      },
      (err: HttpErrorResponse) => {
        if (err.error instanceof Error) {
          this.msg = 'Client error: ' + err.error.message;
        } else {
          this.msg = 'Server error: ' + JSON.stringify(err.error);
        }
      }
    );
  }
}
