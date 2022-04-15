import { 
	Component, 
	HostListener, 
	ElementRef,
	OnInit,
	OnChanges,
	DoCheck,
	AfterViewInit,
	AfterViewChecked,
	AfterContentChecked,
	OnDestroy,
	SimpleChanges,
	AfterContentInit
} from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { Myconfig } from './_services/pb/myconfig';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { CustomerService } from './_services/pb/customer.service';
import { ProductsService } from './_services/pb/products.service';
import { Title, DomSanitizer } from '@angular/platform-browser';
import { TrackingService } from './_services/tracking.service';
import { DataService } from './_services/data.service';
import { SeoService } from './_services/seo.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
	internetStatus = '<b>Sorry, Internet not available, please check internet connection!</b>';
	stripMessage = '';
	headerClass:string;
	homePage:number;
	userId:number;
	timeString:string;
	duration:any;
	seconds:string;
	minutes:string;
	clockDisplay:string;
	interval:any;
	layoutController = '';
	showHeader = false;
	showFooter = false;
	footerActive:boolean = false;
	topScrollStatus:string 	= 'hide';
	sanitizer:any;
	checkStart = 0;
	checkCounter = 0;
	urlAr;
	constructor(
		private config:Myconfig,
		private elem: ElementRef,
		private customer: CustomerService,
		private productService: ProductsService,
		private router: Router, 
		private title: Title, 
		private track:TrackingService,
		private data:DataService,
		private seo:SeoService,
		private sanitize:DomSanitizer
	){
		this.headerClass = '';
		this.homePage 	 = 0;
		this.userId 	 = 0;
		this.timeString  = '';
		this.duration 	 = 20*60;
		this.seconds 	 = "--";
		this.minutes 	 = "--";
		this.sanitizer = sanitize;
	}
	ngOnInit(){
		this.config.scrollToTop(0, 0);
		this.userId = this.customer.getId();
		this.router.events.subscribe(event => {
			if(event instanceof NavigationEnd) {
				var currentTitle = this.getTitle(this.router.routerState, this.router.routerState.root).join('-');
				this.title.setTitle(currentTitle.substring(0, currentTitle.length -1));
				this.userId = this.customer.getId();
			}
			this.seo.createLinkForCanonicalURL();
		});
	    this.getStripMessage();
		this.data.updatedData.subscribe(res => this.headerClass = res.headerClass);
		//this.startTimer();
	
		this.router.events.subscribe(event => {
			if(event instanceof NavigationEnd) {
				this.urlAr = event.url.split('?')[0].split("/");
				let headerCommon = this.config.headerHiddenPages.filter(x => this.urlAr.includes(x));
				let footerCommon = this.config.footerHiddenPages.filter(x => this.urlAr.includes(x));
				this.showHeader = ( headerCommon.length == 0 ) ? true : false;
				this.showFooter = ( footerCommon.length == 0 ) ? true : false;
				//console.log(this.urlAr);
			}
		});
		//console.log(this.showHeader, this.showFooter);		
		if ( this.checkStart ) {
			console.log('I am from ng Do Check() and counter:' + this.checkCounter++);
		}
	}
	
	getStripMessage(){
		this.productService.getStripMessage().subscribe(
		  res => {
			    this.stripMessage = res.data.message;
				if ( navigator.onLine == false ) {
					let temp = this.stripMessage;
					this.stripMessage = this.internetStatus;
					this.internetStatus = temp;
				}
				this.data.updateApprovalStripData(res.data);		
		  },
		  (err: HttpErrorResponse) => {
		  }
		);
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
		if ( this.duration > 0 ) {
			this.interval = setInterval( () => {
				this.duration = this.duration - 1;
				if ( this.duration % 60 < 10 ) {
					this.seconds = "0"+this.duration%60;
				} else {
					this.seconds = (this.duration%60).toString();
				}

				if ( this.duration / 60 < 10 ) {
					this.minutes = "0"+parseInt(""+this.duration/60,10);
				} else {
					this.minutes = ""+parseInt((this.duration / 60).toString(),10);
				}
				this.clockDisplay = this.minutes + " : " +this.seconds;
				if ( this.duration < 1 ) { clearInterval(this.interval); }
		   }, 1000 );
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
	
	@HostListener('window:online') checkOnline() {
		let temp = this.stripMessage;
		this.stripMessage = this.internetStatus;
		this.internetStatus = temp;
	}	
	
	@HostListener('window:offline') checkOffline() {
		let temp = this.stripMessage;
		this.stripMessage = this.internetStatus;
		this.internetStatus = temp;
	}	
}
