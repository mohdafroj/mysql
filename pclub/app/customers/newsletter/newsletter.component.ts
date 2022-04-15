import { Component, Input, OnInit, ViewEncapsulation } from '@angular/core';
//import { Title } from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';

@Component({
  selector: 'pc-newsletter',
  templateUrl: './newsletter.component.html',
  encapsulation: ViewEncapsulation.None,
  styleUrls: [
		'./newsletter.component.css'
	]
})
export class NewsletterComponent implements OnInit {
  newsletterForm:FormGroup;
  response:any;
  resObject = {};
  constructor(private router: Router, private route: ActivatedRoute, private config:Myconfig, private customer: CustomerService) {
  }
  ngOnInit() {
    this.newsletterForm = new FormGroup ({ newsletter: new FormControl(false) });
    this.response = {
      firstname:'',
      lastname:'',
      email:'',
      gender:'',
      dob:'',
      profession:'',
      mobile:'',
      newsletter:false,
      image:'',
    }
    this.customer.getProfile().subscribe(
      res => {
        if(res.status){
          this.response = res.data;
        }
        this.newsletterForm = new FormGroup ({ newsletter: new FormControl(this.response.newsletter) });
      },
      (err: HttpErrorResponse) => {
        console.log("Server Isse!");
      }
    );
  }

  updateNewsletterStatus(formData) {
    this.resObject['message'] = 'Wait...';
	this.resObject['textClass'] = 'text-warning';
    this.customer.updateNewsletterStatus(formData).subscribe(
		res => {
			this.resObject['textClass'] = 'text-success';
			this.resObject['message'] = res.message;
		},
		(err: HttpErrorResponse) => {
			this.resObject['textClass'] = 'text-danger';
			this.resObject['message'] = 'Server issue, try later!';
		}
    );
  }
}
