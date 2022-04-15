import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators } 					from '@angular/forms';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { Myconfig } 											from './../../_services/pb/myconfig';
import { PagesService } 										from './../../_services/pb/pages.service';
import { CustomerService } from './../../_services/pb/customer.service';
//import $ from 'jquery';

@Component({
  selector: 'footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.css']
})
export class FooterComponent implements OnInit {

	rForm:FormGroup;
	msg:string			= '';
	showDevice:boolean 	= false;
	infoActive:string   = '';
	infoShow:string     = '';
	prodActive:string   = '';
	prodShow:string     = '';
	custActive:string   = '';
	custShow:string     = '';
	addrActive:string   = '';
	addrShow:string     = '';
	userId:number		= 0;	
	currentYear:number = 2017;
	constructor(private config:Myconfig, private pages: PagesService, private customer:CustomerService) {
	}

	ngOnInit() {
		this.userId = this.customer.getId();
		this.rForm = new FormGroup ({
			email: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) )
		});
		
		//if(this.window.nativeWindow.innerWidth <= 767){
		//this.showDevice = ($(window).width() <= 767) ? true:false;
		var d = new Date();
		this.currentYear = d.getFullYear();
	}

	newsletterSubscribe(formData){
		this.msg = 'Wait...';
		this.pages.newsletterSubscribe(formData).subscribe(
			res => {
				if(res.status){
					this.rForm = new FormGroup ({
						email: new FormControl("", Validators.compose([Validators.required, Validators.pattern(this.config.EMAIL_REGEXP)]) )
					});
				}
				this.msg = res.message;
				alert(this.msg);
			},
			(err: HttpErrorResponse) => {
				alert("App issue!");
			}
		);
	}
	convertToLower(){
		let newUsername = this.rForm.controls.email.value;
		this.rForm.controls.email.setValue(newUsername.toLowerCase(), {});
	}
	
	footerToggle(num:number){
		if(this.showDevice){
			switch(num){
				case 1:
					this.infoActive		= (this.infoActive == '') ? 'active':'';
					this.infoShow		= (this.infoShow == '')   ? 'show':'';
					this.custActive 	= this.custShow = this.prodActive = this.prodShow = this.addrActive	= this.addrShow	= '';
					break;
				case 2:
					this.custActive		= (this.custActive == '') ? 'active':'';
					this.custShow		= (this.custShow == '')   ? 'show':'';
					this.infoActive 	= this.infoShow = this.prodActive = this.prodShow = this.addrActive	= this.addrShow	= '';
					break;
				case 3:
					this.prodActive		= (this.prodActive == '') ? 'active':'';
					this.prodShow		= (this.prodShow == '')   ? 'show':'';
					this.infoActive 	= this.infoShow = this.custActive = this.custShow = this.addrActive	= this.addrShow	= '';
					break;
				case 4:
					this.addrActive		= (this.addrActive == '') ? 'active':'';
					this.addrShow		= (this.addrShow == '')   ? 'show':'';
					this.infoActive 	= this.infoShow = this.custActive = this.custShow = this.prodActive	= this.prodShow	= '';
					break;
				default:
			}
		}
	}

}
