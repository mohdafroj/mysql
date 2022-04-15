import { Component, OnInit, HostListener, ElementRef, ViewEncapsulation, ViewChild } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ToastrService } from 'ngx-toastr';
import { Router, ActivatedRoute, NavigationEnd } from '@angular/router';
import { HttpErrorResponse, HttpParams } from '@angular/common/http';
import { CustomerService } from './../../../_services/pb/customer.service';
import { StoreService } from './../../../_services/pb/store.service';
import { ProductsService } from './../../../_services/pb/products.service';
import { PagesService } from './../../../_services/pb/pages.service';
import { TrackingService } from './../../../_services/tracking.service';
import { Myconfig } from './../../../_services/pb/myconfig';
import SwiperCore , { Pagination, SwiperOptions, FreeMode } from 'swiper';
//import 'swiper/css/bundle';
import 'swiper/css';
SwiperCore.use([Pagination,FreeMode]);

@Component({
  selector: 'shopnow-dashboard',
  templateUrl: './dashboard.component.html',
  encapsulation: ViewEncapsulation.None,
  styleUrls: [
		'./dashboard.component.css'
  ]
})
export class DashboardComponent implements OnInit {
	userId = 0;
	basePath = '';
	customerCart = [];
	resultStatus = 0;
	resultMsg = '';
	sanitizer;
	productsList = [];
	productsList1 = [];
	productsList2 = [];
	productsList3 = [];
	productsList4 = [];
	productsList5 = [];
	productsList6 = [];
	filterProducts = [];
	queryParamData = {};
	productDetail = {};
	gender = '';
	productType = '';
	oldGender = '';
	oldProductType = '';
	productKey = '';
	selectedTab = 'A';
	selectedIndex = 0;
	sliderConfig = {};
	sliderIndex = 0;
	@ViewChild('deleteConfirmModal', {static: false}) deleteConfirmModal: ElementRef;
	cartTotal = 0;
	cartLength = 0;
	shoppingCart = [];
	shoppingPack = [];
	cartCurrency;
	removeItem = {};
	removeAction = 0;
	stripBanner = '';
	days = '';
	hours = '';
	minutes = '';
	seconds = '';
	swiperConfig: SwiperOptions = {
		slidesPerView: 6,
		spaceBetween: 10,
		loop: false,
		centeredSlides:false,
		freeMode:true,
		breakpoints: {
		  320: {
			slidesPerView: 2.2,
			spaceBetween: 5
		  },
		  640: {
			slidesPerView: 3.5,
			spaceBetween: 5
		  },
		  768: {
			slidesPerView: 4.5,
			spaceBetween: 10
		  },
		  1024: {
			slidesPerView: 6,
			spaceBetween: 10
		  }
		}
  	};
s	
	constructor ( 
		public config: Myconfig,
		private sanitize: DomSanitizer,
		private elem: ElementRef,
		private toastr: ToastrService, 
		private router: Router,
		private route: ActivatedRoute,
		private customer: CustomerService,
		private store: StoreService,
		private track: TrackingService,
		private pages: PagesService,
		private product: ProductsService
	) { 
		this.sanitizer = sanitize;
	}

    ngOnInit() {
		let checkUrl = this.router.url.split("?");
		checkUrl = checkUrl[0].split("/");
		this.basePath = checkUrl[1];
		this.config.scrollToTop();
		this.userId = this.customer.getId(); //this.customer.doEmptyCart();
		let myCart = this.customer.getFromCart();
		this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
		this.route.queryParamMap.subscribe(res => {
			this.gender = (res['params']['gender'] != undefined) ? res['params']['gender'] : '';
			this.productType = (res['params']['type'] != undefined) ? res['params']['type'] : '';
			this.productKey = (res['params']['product'] != undefined) ? res['params']['product'] : '';
			if ( this.gender != this.oldGender || this.productType != this.oldProductType ) {
				this.oldProductType = this.productType;
				this.oldGender = this.gender;
				this.selectedIndex = 0;
			}
			if ( this.gender == '' ) {
				this.categoryFilter();
			} else {
				this.genderFilter();
			}
		});  
		this.getProductList();
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
		this.product.getProducts(prms, 'shopnow').subscribe(
            res => {
                if(res.status){
					this.productsList1 = res.data['productsList1'];
					this.productsList2 = res.data['productsList2'];
					this.productsList3 = res.data['productsList3'];
					this.productsList4 = res.data['productsList4'];
					this.productsList5 = res.data['productsList5'];
					this.productsList6 = res.data['productsList6'];
					this.productsList = this.productsList1.concat(this.productsList2,this.productsList3,this.productsList4,this.productsList5,this.productsList6);
					this.config.setOfferCoupon(res.data['offerCoupon']);
					this.config.setMeta({title: res.data['meta']['title'], keywords: res.data['meta']['keywords'], description: res.data['meta']['description']});
					//this.genderFilter();
					switch(this.productType){
						case this.config.categories.category1.key: this.filterProducts = this.productsList1; break;
						case this.config.categories.category2.key: this.filterProducts = this.productsList2; break;
						case this.config.categories.category3.key: this.filterProducts = this.productsList3; break;
						case this.config.categories.category4.key: this.filterProducts = this.productsList4; break;
						case this.config.categories.category5.key: this.filterProducts = this.productsList5; break;
						case this.config.categories.category6.key: this.filterProducts = this.productsList6; break;
						default:
							this.filterProducts = this.productsList;					}
				}
				this.resultMsg = res.message;
				this.resultStatus = 1;
            },
            (err: HttpErrorResponse) => {
                if(err.error instanceof Error){
					this.resultMsg = err.error.message;
                }else{
					this.resultMsg = JSON.stringify(err.error);
                }
				this.resultStatus = 1;
            }
        );
    }
	categoryChange( argt ) {
		this.router.navigate(['/', this.basePath],{queryParams:{ type:argt}});
	}
	categoryFilter() {
		switch (this.productType) {
			case this.config.categories.category1.key: this.filterProducts = this.productsList1; break;
			case this.config.categories.category2.key: this.filterProducts = this.productsList2; break;
			case this.config.categories.category3.key: this.filterProducts = this.productsList3; break;
			case this.config.categories.category4.key: this.filterProducts = this.productsList4; break;
			case this.config.categories.category5.key: this.filterProducts = this.productsList5; break;
			case this.config.categories.category6.key: this.filterProducts = this.productsList6; break;
			default:
				this.productsList = this.productsList1.concat(this.productsList2,this.productsList3,this.productsList4,this.productsList5,this.productsList6);
				this.filterProducts = this.productsList;

		}
		return true;
	}
	genderChange( argt ) {
		this.router.navigate(['/', this.basePath],{queryParams:{ type:argt, gender:this.gender}});
	}
	genderFilter () {
		switch (this.productType) {
			case this.config.categories.category1.key:
				this.filterProducts = this.productsList1.filter( (item) => { return item.gender == this.gender; }, this.gender);
				break;
			case this.config.categories.category2.key:
				this.filterProducts = this.productsList2.filter( (item) => { return item.gender == this.gender; }, this.gender);
				break;
			case this.config.categories.category3.key:
				this.filterProducts = this.productsList3.filter( (item) => { return item.gender == this.gender; }, this.gender);
				break;
			case this.config.categories.category4.key:
				this.filterProducts = this.productsList4.filter( (item) => { return item.gender == this.gender; }, this.gender);
				break;
			case this.config.categories.category5.key:
				this.filterProducts = this.productsList5.filter( (item) => { return item.gender == this.gender; }, this.gender);
				break;
			case this.config.categories.category6.key:
				this.filterProducts = this.productsList6.filter( (item) => { return item.gender == this.gender; }, this.gender);
				break;
			default: this.filterProducts = this.productsList.filter( (item) => { return item.gender == this.gender; }, this.gender);

		}
		return true;
	}

	getFilterResult () {
		switch (this.productType) {
			case this.config.categories.category1.key:
				if ( this.gender == '' ) {
					this.filterProducts = this.productsList1;
				} else {
					this.filterProducts = this.productsList1.filter( (item) => { return item.gender == this.gender; }, this.gender);
				}
				break;
			case this.config.categories.category2.key:
				if ( this.gender == '' ) {
					this.filterProducts = this.productsList2;
				} else {
					this.filterProducts = this.productsList2.filter( (item) => { return item.gender == this.gender; }, this.gender);
				}
				break;
			case this.config.categories.category3.key:
				if ( this.gender == '' ) {
					this.filterProducts = this.productsList3;
				} else {
					this.filterProducts = this.productsList3.filter( (item) => { return item.gender == this.gender; }, this.gender);
				}
				break;
			case this.config.categories.category4.key:
				if ( this.gender == '' ) {
					this.filterProducts = this.productsList4;
				} else {
					this.filterProducts = this.productsList4.filter( (item) => { return item.gender == this.gender; }, this.gender);
				}
				break;
			case this.config.categories.category5.key:
				if ( this.gender == '' ) {
					this.filterProducts = this.productsList5;
				} else {
					this.filterProducts = this.productsList5.filter( (item) => { return item.gender == this.gender; }, this.gender);
				}
				break;
			case this.config.categories.category6.key:
				if ( this.gender == '' ) {
					this.filterProducts = this.productsList6;
				} else {
					this.filterProducts = this.productsList6.filter( (item) => { return item.gender == this.gender; }, this.gender);
				}
				break;
			default: this.filterProducts = this.productsList.filter( (item) => { return item.gender == this.gender; }, this.gender);

		}
		return true;
	}
	
	viewProduct(item) {
		this.productDetail = item;
		this.track.clickProduct(item);
	}

	viewPage (item) {
		this.router.navigate(['/', item.urlKey]);
	}

}
