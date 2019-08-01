import { Component, HostListener, ElementRef } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { Myconfig } from './_services/pb/myconfig';
import { CustomerService } from './_services/pb/customer.service';
import { Title } from '@angular/platform-browser';
import { TrackingService } from './_services/tracking.service';
import { DataService } from './_services/data.service';
import { SeoService } from './_services/seo.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {

	headerClass:string;
	homePage:number;
	userId:number;
	timeString:string;
	duration:any;
	seconds:string;
	minutes:string;
	clockDisplay:string;
	interval:any;
	footerActive:boolean = false;
	topScrollStatus:string 	= 'hide';

	constructor(
		private config:Myconfig,
		private elem: ElementRef,
		private customer: CustomerService, 
		private router: Router, 
		private title: Title, 
		private track:TrackingService,
		private data:DataService,
		private seo:SeoService
	){
		this.headerClass = '';
		this.homePage 	 = 0;
		this.userId 	 = 0;
		this.timeString  = '';
		this.duration 	 = 20*60;
		this.seconds 	 = "--";
		this.minutes 	 = "--";
	}
	ngOnInit(){
		this.config.scrollToTop(0, 0);
		this.userId = this.customer.getId();
		this.router.events.subscribe(event => {
			if(event instanceof NavigationEnd) {
				var currentTitle = this.getTitle(this.router.routerState, this.router.routerState.root).join('-');
				this.title.setTitle(currentTitle.substring(0, currentTitle.length -1));
				let urlAr:any = event.url.split('/'); 
				this.footerActive = ( (urlAr[2] == 'cart' || urlAr[2] == 'onepage') && (urlAr[3] == undefined) ) ? false : true;	
				this.userId = this.customer.getId();
			}
			this.seo.createLinkForCanonicalURL();
		});
	    
		this.data.updatedData.subscribe(res => this.headerClass = res.headerClass);
		//this.startTimer();
		
	}
	
	getTitle(state, parent) {
		var data = [];
		if(parent && parent.snapshot.data && parent.snapshot.data.title) {
		  data.push(parent.snapshot.data.title);
		}

		if(state && parent) {
		  data.push(this.getTitle(state, state.firstChild(parent)));
		}
		return data;
	}

	scrollToTop(){
		this.config.scrollToTop(0, 0);
	}
  
  startTimer(){
    if(this.duration > 0){
      this.interval = setInterval( () => {
        this.duration = this.duration - 1;
        if(this.duration % 60 < 10){
          this.seconds = "0"+this.duration%60;
        }else{
          this.seconds = (this.duration%60).toString();
        }

        if(this.duration / 60 < 10 ){
          this.minutes = "0"+parseInt(""+this.duration/60,10);
        }else{
          this.minutes = ""+parseInt((this.duration / 60).toString(),10);
        }
        this.clockDisplay = this.minutes + " : " +this.seconds;
        if( this.duration < 1 ){ clearInterval(this.interval); }
      },1000);
    }
  }
  
	@HostListener('window:scroll') checkScroll() {
		const scrollPosition:number = window.pageYOffset;
		if( scrollPosition > 50 ){
			this.topScrollStatus = 'show';
		}else{
			this.topScrollStatus = 'hide';
		}
	}
}
