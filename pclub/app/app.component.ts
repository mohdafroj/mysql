import { 
  Component, HostListener, ElementRef,
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
import { HttpErrorResponse } from '@angular/common/http';
import { Title, Meta, MetaDefinition, DomSanitizer } 		from '@angular/platform-browser';
import { NavigationEnd, Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';
import { Myconfig } from './_services/pb/myconfig';
import { CustomerService } from './_services/pb/customer.service';
import { TrackingService } from './_services/tracking.service';
import { DataService } from './_services/data.service';
import { SeoService } from './_services/seo.service';
import { PagesService } from './_services/pb/pages.service';

@Component({
  selector: 'body',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {	
	stripBanner = '';
	headerClass:string;
	homePage:number;
	userId:number;
	topScrollStatus:string 	= 'hide';
	layoutController = '';
	showHeader = false;
	showFooter = false;
	sanitizer;
	checkStart = 0;
	checkCounter = 0;
	urlAr;
	constructor(
		private cookieService: CookieService,
		private config:Myconfig,
		private elem: ElementRef,
		private customer: CustomerService, 
		private router: Router, 
		private title: Title, 
		private meta: Meta, 
		private track:TrackingService,
		private data:DataService,
		private seo:SeoService,
		private pages: PagesService,
		private sanitize: DomSanitizer
	){
		this.headerClass = '';
		this.homePage 	 = 0;
		this.userId 	 = 0;
		this.sanitizer = sanitize;
	}
	
	ngOnInit(){
		this.cookieService.set('sameSite', 'Strict'); //Lax|Strict|None
		this.pages.companyData().subscribe(
		  res => {
			let infoData = res['data']['company'];
			this.pages.setCompanyData(res['data']);
			this.stripBanner = ( ( infoData != undefined ) && (infoData['strip'] != undefined) && (infoData['strip'] != '') ) ? infoData['strip'] : '';
		  },
		  (err: HttpErrorResponse) => {
		  }
		);
		this.config.scrollToTop(0, 0);
		this.userId = this.customer.getId();
		this.router.events.subscribe(event => {
			this.seo.createLinkForCanonicalURL();
		});
		let jsonData = 
		{
		};
	    this.seo.createJsonLd(jsonData);
		this.data.updatedData.subscribe(res => this.headerClass = res.headerClass);
		if ( this.checkStart ) {
			console.log('I am from ngOnInit() and counter:' + this.checkCounter++);
		}
		//this.startOfferTimer();
	}
	whatsAppChat () {
		let infoData = this.pages.getCompanyData();
		let company = ( (infoData != undefined) && (infoData['company'] != undefined) ) ? infoData['company'] : {phone:''};
		window.location.href = 'https://api.whatsapp.com/send/?phone='+company.phone+'&text=Hi, I need some Help';
	}
	ngOnChanges() {
		if ( this.checkStart ) {
			console.log('I am from ngOnChanges() and counter:' + this.checkCounter++);
		}
	}
	
	ngDoCheck() {
		this.router.events.subscribe(event => {
			if(event instanceof NavigationEnd) {
				var currentTitle = this.getTitle(this.router.routerState, this.router.routerState.root).join('-');
				this.title.setTitle(currentTitle.substring(0, currentTitle.length -1));
				this.urlAr = event.url.split('/');
				let checkUrl = this.urlAr[1].split("?");
				checkUrl = ( checkUrl[0] == "") ? "home" : checkUrl[0];
				if ( this.config.metaInfo[checkUrl]  ) {
					this.title.setTitle(this.config.metaInfo[checkUrl]['title']);
					let keyword: MetaDefinition = { name: 'keywords', content: this.config.metaInfo[checkUrl]['keyword']};
					this.meta.addTag(keyword,true);
					let description: MetaDefinition = { name: 'description', content: this.config.metaInfo[checkUrl]['description']};
					this.meta.addTag(description,true);
				} //console.log(this.urlAr);
				if ( this.urlAr == undefined ) {
					this.showHeader = this.showFooter = true;
				} else {
					this.showFooter = ( this.config.footerHiddenPages.indexOf(checkUrl) != -1 ) ? false : true;					
					this.userId = this.customer.getId();
					if ( this.config.headerHiddenPages.indexOf(checkUrl) != -1 ) {
						this.layoutController = checkUrl;
						this.showHeader = this.showFooter = false;
						this.elem.nativeElement.ownerDocument.body.className = 'pattern_image';
					} else {
						this.showHeader = true;
						this.layoutController = '';
						this.elem.nativeElement.ownerDocument.body.style['overflow-y'] = 'unset';
						this.elem.nativeElement.ownerDocument.body.className = '';
					}
				}
			}
		});
		//console.log(this.showHeader, this.showFooter);		
		if ( this.checkStart ) {
			console.log('I am from ngDoCheck() and counter:' + this.checkCounter++);
		}
	}
	
	ngAfterContentInit() {
		if ( this.checkStart ) {
			console.log('I am from ngAfterContentInit() and counter:' + this.checkCounter++);
		}
	}
	
	ngAfterContentChecked() {
		if ( this.checkStart ) {
			console.log('I am from ngAfterContentChecked() and counter:' + this.checkCounter++);
		}
	}
	
	ngAfterViewInit() {
		if ( this.checkStart ) {
			console.log('I am from ngAfterViewInit() and counter:' + this.checkCounter++);
		}
	}
	
	ngAfterViewChecked() {
		if ( this.checkStart ) {
			console.log('I am from ngAfterViewChecked() and counter:' + this.checkCounter++);
		}
	}
	
	ngOnDestroy() {
		if ( this.checkStart ) {
			console.log('I am from ngOnDestroy() and counter:' + this.checkCounter++);
		}
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
  
	startOfferTimer () {
		let abc = this.config.stopSale();
		let currentDate = this.config.getCurrentDate();
		let offerDate = this.config.getOfferDate();		
		if ( abc != "" ) {
			this.stripBanner = 'Sale Ends In ' + abc;
		} else {
			this.config.setOfferCoupon(''); //set offer coupon empty
			let infoData = this.pages.getCompanyData();
			this.stripBanner = ( ( infoData != undefined ) && (infoData['company'] != undefined) && (infoData['company']['strip'] != undefined) && (infoData['company']['strip'] != '') ) ? infoData['company']['strip'] : '';
		}
		setTimeout( () => { this.startOfferTimer(); }, 1000);
	}
	
	/*startTimer (duration) {
		let seconds = '';
		let minutes = '';
		let hours = '';
		let days = '';
		if ( duration > 0 ) {
			let intervalId = setInterval( () => {
				duration = duration - 1;
				this.config.setOfferDuration(duration);
				let durationToDays = parseInt((duration / (24 * 3600)).toString(), 10);
				days = "" + parseInt(durationToDays.toString(),10);
				
				let dayToHour = parseInt(((duration - parseInt(days) * 24 * 3600)/3600).toString(), 10);
				if ( dayToHour < 10 ) {
					hours = "0" + parseInt(dayToHour.toString(), 10);
				} else {
					hours = "" + parseInt(dayToHour.toString(),10);
				}				
				//console.log(parseInt((duration - parseInt(days) * 24 * 3600 - parseInt(hours) * 3600)/60));
				let hourToMin = parseInt(((duration - parseInt(days) * 24 * 3600 - parseInt(hours) * 3600)/60).toString(), 10);
				if ( hourToMin < 10 ) {
					minutes = "0" + parseInt(hourToMin.toString(), 10);
				} else {
					minutes = "" + parseInt(hourToMin.toString(),10);
				}
				
				if ( duration % 60 < 10 ) {
					seconds = "0" + duration % 60;
				} else {
					seconds = (duration % 60).toString();
				}
				
				if ( parseInt(days) > 0 ) {
					this.stripBanner = days + " days " + hours + " : " + minutes + " : " + seconds;
				} else  if ( parseInt(hours) > 0 ) {
					this.stripBanner = hours + " : " + minutes + " : " + seconds;
				} else {
					this.stripBanner = minutes + " : " + seconds;
				}
				this.stripBanner = 'Sale Ends In ' + this.stripBanner;
				
				if ( duration < 1 ) {
					clearInterval(intervalId);
					this.config.setOfferCoupon(''); //set offer coupon empty
					this.config.setOfferDuration(0); //set offer duration to 0
					let infoData = this.pages.getCompanyData();
					this.stripBanner = ( ( infoData != undefined ) && (infoData['company'] != undefined) && (infoData['company']['strip'] != undefined) && (infoData['company']['strip'] != '') ) ? infoData['company']['strip'] : '';
				}
			}, 1000);
		}
	}*/
  
	@HostListener('window:scroll') checkScroll() {
		const scrollPosition:number = window.pageYOffset;
		if( scrollPosition > 50 ){
			this.topScrollStatus = 'show';
		}else{
			this.topScrollStatus = 'hide';
		}
	}
}
