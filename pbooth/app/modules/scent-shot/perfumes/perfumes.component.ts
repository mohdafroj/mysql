import { Component, OnInit, ElementRef, HostListener, ViewChild } from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } from '@angular/platform-browser';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Router, ActivatedRoute, NavigationEnd, Params } from '@angular/router';
import { Myconfig } from './../../../_services/pb/myconfig';
import { ProductsService } from './../../../_services/pb/products.service';
import { CustomerService } from '../../../_services/pb/customer.service';
import { StoreService } from './../../../_services/pb/store.service';
import { TrackingService } from './../../../_services/tracking.service';
import { SeoService } from './../../../_services/seo.service';
import { DataService } from './../../../_services/data.service';

import { NguCarousel, NguCarouselConfig, NguCarouselStore } from '@ngu/carousel';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-perfumes',
  templateUrl: './perfumes.component.html',
  styles: [`
     .kit_contains_div p{color: rgba(0, 0, 0, 0.7); font-size: 16px; padding: 0 0 2px 0; line-height: 20px; margin: 0}
  `],
  styleUrls: ['./perfumes.component.css']
})
export class PerfumesComponent implements OnInit {	
    resultSlide:any					= [];
	activeSlide:number              = 0;
    result:any						= [];
	productType:string				= 'product';
	resultMsg:string				= '';
    userId:number					= 0;

	category:number					= 0;
	gender:string					= '';
	queryParameter:any				= {};
	topScrollClass:string 	= 'affix-top';
	currentPath:string = '';
	
	nguFirst: NguCarouselConfig;
	nguSecond: NguCarouselConfig;
	
	nguFirstToken:string;
	nguSecondToken:string;
	
	@ViewChild('nguCarouselFirst', {static: false}) nguCarouselFirst: NguCarousel<any>;
	@ViewChild('nguCarouselSecond', {static: false}) nguCarouselSecond: NguCarousel<any>;	
	sanitizer:any;
    constructor (
		private sanitize: DomSanitizer, 
		private toastr:ToastrService, 
		private elem:ElementRef, 
		private meta:Meta, 
		private title: Title, 
		private products: ProductsService, 
		private router: Router, 
		private route: ActivatedRoute, 
		private customer: CustomerService, 
		private store: StoreService, 
		private config:Myconfig, 
		private track:TrackingService,
		private dataService: DataService,
		private seo: SeoService
	){
		this.sanitizer = sanitize;
		let urls = route.snapshot.url;
		this.currentPath 	= ( Array.isArray(urls) && urls[0] && urls[0].path ) ? urls[0].path : '';
    }
    
	ngOnInit() {
		this.userId = this.customer.getId();
		this.config.scrollToTop();
		let title = 'Buy Large Perfume Bottle || Big Perfume Bottle';
		let metaDescription = 'Shop now large perfume bottle for long lasting at low price. Big Perfume Bottles are available for international brands with exciting offer.';
		this.title.setTitle(title);
		let keyword: MetaDefinition = { name: 'keywords', content: 'perfumes, international brands perfumes, perfume for men and women, perfume bottle'};
		this.meta.addTag(keyword);
		let description: MetaDefinition = { name: 'description', content: metaDescription};
		this.meta.addTag(description);
		this.seo.ogMetaTag(
			'Buy Large Perfume Bottle || Big Perfume Bottle',
			'Shop now large perfume bottle for long lasting at low price. Big Perfume Bottles are available for international brands with exciting offer.',
			'https://www.perfumebooth.com/assets/images/home/perfume.png'
		);
		
		this.route.queryParams.subscribe((params: Params) => {
			this.queryParameter = params;
            switch(params.gender){
				case 'male': this.gender = params.gender; break;
				case 'female': this.gender = params.gender; break; 
				default: this.gender = '';
			}
			this.getScentShot();
        });
		this.nguFirst = {
			grid: {xs: 3.5, sm: 6, md: 6, lg: 6, all: 0},
			slide: 3,
			speed: 400,
			point: {
				visible: true,
				hideOnSingleSlide:true
			},
			load: 1,
			easing: 'ease'
		};
		this.nguSecond = {
			grid: {xs: 1, sm: 1, md: 1, lg: 1, all: 0},
			slide: 1,
			speed: 2000,
			interval: {
				timing:2000,
				initialDelay:1000
			},
			point: {
				visible: true,
				hideOnSingleSlide:true
			},
			load: 1,
			loop: true,
			easing: 'ease'
		};
	}
	
	initDataFirstFn(key: NguCarouselStore){
		this.nguFirstToken = key.token;
	}

	initDataSecondFn(key: NguCarouselStore){
		this.nguSecondToken = key.token;
	}

	getScentShot(){
		this.productType	= 'product';
		this.resultMsg 		= 'Loading...';
		this.result = [];
		this.resultSlide = [];
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		prms = prms.append('category-id', `${this.category}`);
		for (let key in this.queryParameter){
            prms = prms.append(key, this.queryParameter[key]);
        }
		
		this.products.getSkuProducts(prms).subscribe(
            res => { //console.log(res.data[0]);
				if( res.data.length > 0 ){
					this.resultSlide = res.data;
					this.result.push(this.resultSlide[0]);
					this.dataService.sendReviews(this.result[0]);
				}
				this.resultMsg = '';
				this.activeSlide = 0;
            },
            (err: HttpErrorResponse) => {
                if(err.error instanceof Error){
                    console.log('Client Error: '+err.error.message);
                }else{
                    console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
                }
            }
        );
	}
	
	addCart(itemId){
		localStorage.setItem('productId', itemId);
		if(this.userId > 0){
			let formData:any = {itemId:itemId,qty:1};
			this.store.addToCart(formData).subscribe(
			  res => {
				if( res.status ){
					this.customer.setCart(res.data.cart);
					for(let i of this.resultSlide){
						if( itemId == i.id ){ i.isCart = 1; }
					}
					for(let i of this.result){
						if( itemId == i.id ){ i.isCart = 1; }
					}
					this.toastr.success(res.message);
					let myCart:any = this.customer.getCart();
					for(let i=0; i < myCart.length; i++){
						if( myCart[i]['id'] == itemId ){
							this.track.addToCart(myCart[i]);
							break;
						}
					}
				}else{
					this.toastr.error(res.message);
				}
			  },
			  (err: HttpErrorResponse) => {
				this.toastr.error("Sorry, there are some app issue!");
			  }
			);
		}else{
			this.router.navigate(['/customer/registration']);
		}
	}
	  
	resultSlides(index:number){
		//this.nguCarouselSecond.reset(true);
		this.result 			= [];
		this.productType		= 'product';
		this.activeSlide 		= index;
		this.result.push(this.resultSlide[this.activeSlide]);
		this.dataService.sendReviews(this.result[0]);
		return false;
	}
	
	selectGender(gen:string){
		if( gen != this.gender ){
			this.nguCarouselFirst.reset(true);	
			this.gender = gen;
			this.getScentShot();
		}
	}
	
	getFinalRating(num){
		return this.config.numToArray(num);
	}
	
	getRemainingRating(num){
		let a = 5 - num;
		return this.config.numToArray(a);
	}
	
	notifyMePopup(itemId){
		this.dataService.sendNotifyme({userId: this.userId, productId: itemId});
	}
	
	@HostListener('window:scroll') checkScroll() {
		const scrollPosition:number = window.pageYOffset;
		if( scrollPosition > 400 ){
			this.topScrollClass = 'affix';
		}else{
			this.topScrollClass = 'affix-top';
		}
	}

	
}

