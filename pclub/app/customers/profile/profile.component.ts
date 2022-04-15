import { Component, OnInit, ViewEncapsulation, ElementRef } from '@angular/core';
//import { Title } from '@angular/platform-browser';
import { DOCUMENT } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';

@Component({
  selector: 'pc-profile',
  templateUrl: './profile.component.html',
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {
	response: any;
	state: string;
	refLink: string;
	modulePath = '';
	constructor (
		private elem: ElementRef, 
		private router: Router, 
		private route: ActivatedRoute, 
		private config:Myconfig, 
		private customer: CustomerService
	) {		
    }

  ngOnInit() {
	this.config.scrollToTop();
    this.refLink = window.location.origin;
    if(window.location.href.indexOf('/new/') > -1){
      this.refLink = this.refLink+'/new';
    }
	//ClipBoard.test();
	//(window as any).copyTextToClipboard('my clipboard copy test');
    this.response = {
      firstname:'',
      lastname:'',
      email:'',
      gender:'',
      dob:'',
      mobile:'',
      image:'',
      created:'',
      modified:''
    };
    this.customer.getProfile().subscribe(
      res => { 
        if(res.status){
          this.response = res.data;
          this.refLink = this.refLink+'/customer/registration?ref='+this.customer.getId();		  
        }
      },
      (err: HttpErrorResponse) => {
        console.log("Server Isse!");
      }
    );	
  }

	copyLink(){
		var aux = document.createElement("input");
		aux.setAttribute("value", this.refLink);
		document.body.appendChild(aux);
		aux.select();
		document.execCommand("copy");
		document.body.removeChild(aux);
		return false;
	}


}
