import { Component, OnInit, ElementRef } 				from '@angular/core';
import { Location } 											from '@angular/common';
import { Router, NavigationEnd } 					from '@angular/router';
import { CustomerService } from './../../_services/pb/customer.service';
import { DataService } from './../../_services/data.service';

@Component({
  selector: 'header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {
	toggledClass:string;
	buttonClass:string;	
	customDir:string;
	headerClass:string;
	priveMember:boolean = false;
	
	secondParam:string = '';
	thirdParam:string = '';
	constructor(private loc:Location, private auth: CustomerService, private data: DataService, private router: Router, private elem:ElementRef) {
		this.toggledClass = '';
		this.buttonClass  = '';	
		this.customDir  = '';
		this.headerClass  = '';
	}
	
	ngOnInit(){
        this.router.events.subscribe(event => {
            if(event instanceof NavigationEnd) {
				this.toggledClass 		= '';
				this.buttonClass 		= '';
				this.secondParam 	= event.url.split('/')[1]; 
				this.thirdParam 	= event.url.split('/')[2]; 
            }
        });
		this.data.customDirData.subscribe(res => this.customDir = res.customDir);
		this.data.updatedData.subscribe(res => this.headerClass = res.headerClass);
		this.updateSiteLogo();
	}
	
	updateSiteLogo(){
		let prive:number = this.auth.getPrive();
		if( prive == 1 ){
			this.priveMember = true;
		}else{
			this.priveMember = false;
		}
		setTimeout(()=>{ this.updateSiteLogo(); }, 2000);
	}

	toggleMenu(){
		this.toggledClass = ( this.toggledClass == '' ) ? 'toggled':'';
		this.buttonClass  = ( this.buttonClass == '' ) ? 'active':'';
	}
	
	toggleMenuHeader(event){
		if (!this.elem.nativeElement.contains(event.target)){
			this.toggledClass 		= '';
			this.buttonClass 		= '';
		}		
	}
	
	historyBack(){
		this.loc.back();
	}

}
