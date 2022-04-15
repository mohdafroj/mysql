import { Component, OnInit, HostListener, ElementRef, ViewEncapsulation } from '@angular/core';
import { DomSanitizer } 		from '@angular/platform-browser';
import { HttpErrorResponse, HttpParams } from '@angular/common/http';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { Myconfig } 	from './../../_services/pb/myconfig';
import { PagesService } from './../../_services/pb/pages.service';
import { ProductsService } from './../../_services/pb/products.service';
import { StoreService } from './../../_services/pb/store.service';
//import $ from 'jquery';
import SwiperCore , { Pagination, SwiperOptions, FreeMode } from 'swiper';
//import 'swiper/css/bundle';
import 'swiper/css';
SwiperCore.use([Pagination,FreeMode]);
@Component({
  selector: '.home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
	youtubelink = '';
	resultStatus = 0;
	productsList1 = [];
	productsList2 = [];
	productsList3 = [];
	productsList4 = [];
	productsList5 = [];
	productsList6 = [];
	productsList7 = [];
	filterProduct1 = [];
	filterProduct2 = [];
	filterProduct3 = [];
	filterProduct4 = [];
	filterProduct5 = [];
	filterProduct6 = [];
	filterProduct7 = [];
	gender1 = 'emale';
	gender2 = 'emale';
	gender3 = 'emale';
	gender4 = 'emale';
	gender5 = 'emale';
	gender6 = 'emale';
	gender7 = 'emale';
	productMessage = 'Sorry, records are not found!';
	sanitizer;
	swiperConfig: SwiperOptions = {
		slidesPerView: 2.5,
		spaceBetween: 10,
		loop: false,
		centeredSlides:false,
		freeMode:true,
		breakpoints: {
		  320: {
			slidesPerView: 1.5,
			spaceBetween: 5
		  },
		  640: {
			slidesPerView: 1.2,
			spaceBetween: 5
		  },
		  768: {
			slidesPerView: 2.2,
			spaceBetween: 10
		  },
		  1024: {
			slidesPerView: 2.5,
			spaceBetween: 10
		  }
		}
  	};
	
	constructor( 
		private config: Myconfig, 
		private el:ElementRef, 
		private sanitize: DomSanitizer,
		private router: Router,
		private route: ActivatedRoute,
		private pages: PagesService,
		private product: ProductsService,
		private store: StoreService
	) {
  		//console.log("home constructor called");
		this.sanitizer = sanitize;
    }
    
	ngOnInit() {
		this.config.scrollToTop();
		this.getProductList();
		let infoData = this.pages.getCompanyData();
		this.youtubelink = (infoData && infoData['company'] && infoData['company']['homeyoutube']) ? infoData['company']['homeyoutube'] : '#';
    }
	
	slickInit(e) {
		//console.log('slick initialized');
	}
	
	scrollToElement(scrollID): void {
		//$("html, body").animate({ scrollTop: ($(scrollID).offset().top) }, 1000);
	}
	
	getProductList () {
		this.resultStatus   = 0;
		let offerCoupon = this.config.getOfferCoupon();
		let inputData = this.store.getCartInfo();
		if ( offerCoupon != '' ) {
			inputData['couponCode'] = offerCoupon;
			this.store.setCartInfo(inputData);
		}
		let prms = new HttpParams();
		prms = prms.append('offerCoupon', offerCoupon);
		this.product.getProducts(prms, 'home').subscribe(
            res => {
                if(res.status){
					this.productsList1 = res.data['productsList1'];
					this.productsList2 = res.data['productsList2'];
					this.productsList3 = res.data['productsList3'];
					this.productsList4 = res.data['productsList4'];
					this.productsList5 = res.data['productsList5'];
					this.productsList6 = res.data['productsList6'];
					this.productsList7 = res.data['productsList7'];
					this.config.setOfferCoupon(res.data['offerCoupon']);
					this.filterProduct1 = this.productsList1;
					this.filterProduct2 = this.productsList2;
					this.filterProduct3 = this.productsList3;
					this.filterProduct4 = this.productsList4;
					this.filterProduct5 = this.productsList5;
					this.filterProduct6 = this.productsList6;
					/*this.genderFilter(1, this.gender1);
					this.genderFilter(2, this.gender2);
					this.genderFilter(3, this.gender3);
					this.genderFilter(4, this.gender4);
					this.genderFilter(5, this.gender5);
					this.genderFilter(6, this.gender6);
					this.genderFilter(7, this.gender7);*/
				}
				this.resultStatus = 1;
            },
            (err: HttpErrorResponse) => {
				this.resultStatus = 1;
            }
        );
    }

	genderFilter (sliderNumber, value) {
		switch(sliderNumber) {
			case 1: 
				this.gender1 = value;
				this.filterProduct1 =  this.productsList1.filter( (item) => { return item.gender == this.gender1; }, this.gender1);
				break;
			case 2:
				this.gender2 = value;
				this.filterProduct2 =  this.productsList2.filter( (item) => { return item.gender == this.gender2; }, this.gender2);
				break;
			case 3:
				this.gender3 = value;
				this.filterProduct3 =  this.productsList3.filter( (item) => { return item.gender == this.gender3; }, this.gender3);
				break;
			case 4:
				this.gender4 = value;
				this.filterProduct4 =  this.productsList4.filter( (item) => { return item.gender == this.gender4; }, this.gender4);
				break;
			case 5:
				this.gender5 = value;
				this.filterProduct5 =  this.productsList5.filter( (item) => { return item.gender == this.gender5; }, this.gender5);
				break;
			case 6:
				this.gender6 = value;
				this.filterProduct6 =  this.productsList6.filter( (item) => { return item.gender == this.gender6; }, this.gender6);
				break;
			case 7:
				this.gender7 = value;
				this.filterProduct7 =  this.productsList7.filter( (item) => { return item.gender == this.gender7; }, this.gender7);
				break;
			default:
		}
		return true;
	}
	
	viewPage (item) {
		this.router.navigate(['/', item.urlKey]);
	}

}
