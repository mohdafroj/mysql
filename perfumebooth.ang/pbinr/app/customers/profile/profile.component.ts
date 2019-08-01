import { Component, OnInit,ElementRef } from '@angular/core';
//import { Title } from '@angular/platform-browser';
import { DOCUMENT } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } 		from './../../_services/pb/myconfig';
import { CustomerService }  from './../../_services/pb/customer.service';
import { SeoService } 	   	from './../../_services/seo.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./../../../assets/css/user-dashboard.css','./profile.component.css']
})
export class ProfileComponent implements OnInit {
  response:any;
  state:string;
  refLink:string;
  constructor(private seo: SeoService, private elem:ElementRef, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
  }

  ngOnInit() {
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
      profession:'',
      address:'',
      city:'',
      pincode:'',
      mobile:'',
      image:'',
      location_id:0,
      created:'',
      modified:'',
      áddresses:[],
      locations:[]
    };
    this.auth.getProfile().subscribe(
      res => {
        if(res.status){
          this.response = res.data;

          this.refLink = this.refLink+'/customer/registration?ref='+this.auth.getId();
          let i:number;
          for(i=0; i < this.response.locations.length; i++){
            if( this.response.locations[i]['id'] == this.response.location_id ){
              this.state = this.response.locations[i]['title']; break;
            }
          }
		  
        }
      },
      (err: HttpErrorResponse) => {
        console.log("Server Isse!");
      }
    );
	
	this.seo.ogMetaTag('Profile Page', 'Profile page description');
	
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
