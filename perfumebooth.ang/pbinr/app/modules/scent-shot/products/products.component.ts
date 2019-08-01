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
  selector: 'app-products',
  templateUrl: './products.component.html',
  styles: [`
  		.kit_contains_div p{color: rgba(0, 0, 0, 0.7); font-size: 16px; padding: 0 0 2px 0; line-height: 20px; margin: 0}
  `],
  styleUrls: ['./products.component.css']
})
export class ProductsComponent implements OnInit {
    resultSlide: any					= [];
    activeSlide              = 0;
    result: any						= [];
    productType				= 'product';
    resultMsg				= '';
    userId					= 0;

    category					= 7;
    gender			= '';
    queryParameter: any				= {};
    topScrollClass	= 'affix-top';
    currentPath	= '';

    nguFirst: NguCarouselConfig;
    nguSecond: NguCarouselConfig;

    nguFirstToken: string;
    nguSecondToken: string;
    winWidth = 0;

    @ViewChild('nguCarouselFirst') nguCarouselFirst: NguCarousel<any>;
    @ViewChild('nguCarouselSecond') nguCarouselSecond: NguCarousel<any>;
    sanitizer: any;

    @HostListener('window:load') onLoad() {
        this.winWidth = window.innerWidth;
    }

    @HostListener('window:scroll') checkScroll() {
        const scrollPosition: number = window.pageYOffset;
        if ( scrollPosition > 400 ) {
            this.topScrollClass = 'affix';
        } else {
            this.topScrollClass = 'affix-top';
        }
    }

    constructor (
        private sanitize: DomSanitizer,
        private toastr: ToastrService,
        private elem: ElementRef,
        private meta: Meta,
        private title: Title,
        private products: ProductsService,
        private router: Router,
        private route: ActivatedRoute,
        private customer: CustomerService,
        private store: StoreService,
        private config: Myconfig,
        private track: TrackingService,
		private dataService: DataService,
        private seo: SeoService
    ) {
        this.sanitizer = sanitize;
        const urls = route.snapshot.url;
        this.currentPath 	= urls[0].path;
        this.winWidth = window.innerWidth;
    }

    ngOnInit() {
        this.userId = this.customer.getId();
        this.config.scrollToTop();
        const title = 'Buy Perfume Scent shot || Best Niche Perfume Brands';
        const metaDescription = `Browse luxury choice fragrance of top international brands and order your dream
            perfume at low price. We provide doorstep delivery in India.`;
        this.title.setTitle(title);
        const keyword: MetaDefinition = { name: 'keywords', content: `perfume scent shot, scent shot, luxury perfume, international
            brands perfumes, scent shot refill pack, perfume for men, perfume for girls`};
        this.meta.addTag(keyword);
        const description: MetaDefinition = { name: 'description', content: metaDescription};
        this.meta.addTag(description);
        this.seo.ogMetaTag(
			'Buy Perfume Scent shot || Best Niche Perfume Brands',
			'Browse luxury choice fragrance of top international brands and order your dream perfume at low price. We provide doorstep delivery in India.',
			'https://www.perfumebooth.com/assets/images/home/scent-shot.png'
		);
        this.route.queryParams.subscribe((params: Params) => {
            this.queryParameter = params;
            switch (params.gender) {
                case 'male': this.gender = params.gender; break;
                case 'female': this.gender = params.gender; break;
                default: this.gender = '';
            }
            this.getScentShot();
        });
        this.nguFirst = {
            grid: {xs: 3, sm: 6, md: 6, lg: 6, all: 0},
            slide: 3,
            speed: 400,
            point: {
                visible: true,
                hideOnSingleSlide: true
            },
            load: 1,
            easing: 'ease'
        };
        this.nguSecond = {
            grid: {xs: 1, sm: 1, md: 1, lg: 1, all: 0},
            slide: 1,
            speed: 2000,
            interval: {
                timing: 2000,
                initialDelay: 1000
            },
            point: {
                visible: true,
                hideOnSingleSlide: true
            },
            load: 1,
            loop: true,
            easing: 'ease'
        };
    }

    initDataFirstFn(key: NguCarouselStore) {
        this.nguFirstToken = key.token;
    }

    initDataSecondFn(key: NguCarouselStore) {
        this.nguSecondToken = key.token;
    }

    getScentShot() {
        this.productType	= 'product';
        this.resultMsg 		= 'Loading...';
        this.result = [];
        this.resultSlide = [];
        let prms = new HttpParams();
        prms = prms.append('userId', `${this.userId}`);
        prms = prms.append('category-id', `${this.category}`);
        for (const key in this.queryParameter) {
            if ( key === 'userId' || key === 'category-id' ) {
            } else {
                prms = prms.append(key, this.queryParameter[key]);
            }
        }
        this.products.getOfferProducts(prms).subscribe(
            res => { // console.log(prms);
                this.resultSlide = res.data;
                this.result.push(this.resultSlide[0]);
				this.dataService.sendReviews(this.resultSlide[0]);
                this.resultMsg = '';
                this.activeSlide = 0;
            },
            (err: HttpErrorResponse) => {
                if (err.error instanceof Error) {
                    console.log('Client Error: ' + err.error.message);
                } else {
                    console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
                }
            }
        );
    }

    addCart(itemId) {
        localStorage.setItem('productId', itemId);
        if (this.userId > 0) {
            const formData: any = {itemId: itemId, qty: 1};
            this.store.addToCart(formData).subscribe(
              res => {
                if ( res.status ) {
                    this.customer.setCart(res.data.cart);
                    for ( const i of this.resultSlide) {
                        if ( itemId === i.id ) { i.isCart = 1; }
                    }
                    for ( const i of this.result) {
                        if ( itemId === i.id ) { i.isCart = 1; }
                    }
                    this.toastr.success(res.message);
                    const myCart: any = this.customer.getCart();
                    for ( let i = 0; i < myCart.length; i++) {
                        if ( myCart[i]['id'] === itemId ) {
                            this.track.addToCart(myCart[i]);
                            break;
                        }
                    }
                } else {
                    this.toastr.error(res.message);
                }
              },
              (err: HttpErrorResponse) => {
                this.toastr.error('Sorry, there are some app issue!');
              }
            );
        } else {
            this.router.navigate(['/customer/registration']);
        }
    }

    resultSlides(index: number) {
        // this.nguCarouselSecond.reset(true);
        this.result 			= [];
        this.productType		= 'product';
        this.activeSlide 		= index;
        this.result.push(this.resultSlide[this.activeSlide]);
		this.dataService.sendReviews(this.resultSlide[this.activeSlide]);
        return false;
    }

    selectGender(gen: string) {
        if ( gen !== this.gender ) {
            this.nguCarouselFirst.reset(true);
            this.gender = gen;
            this.getScentShot();
        }
    }

    getFinalRating(num) {
        return this.config.numToArray(num);
    }

    getRemainingRating(num) {
        const a = 5 - num;
        return this.config.numToArray(a);
    }
	
	productPopup(index, pIndex){
		this.dataService.sendPopupProduct({index: index, items: this.result[pIndex]['related']});
		return false;
	}

    notifyMePopup(itemId) {
		this.dataService.sendNotifyme({userId: this.userId, productId: itemId});
    }

    subProduct(sku: string, code: string) {
        const oldProduct = this.productType;
        let prms = new HttpParams();
        prms = prms.append('userId', `${this.userId}`);
        if ( code === 'refill' ) {
            prms = prms.append('category-id', '4'); // perfume selfie category id
        } else {
            prms = prms.append('category-id', `${this.category}`);
        }
        prms = prms.append('sku_code', sku);
        if ( code === 'combo' ) {
            prms = prms.append('combo', '1');
        }
        if ( code !== this.productType ) {
            this.productType = code;
            this.products.getOfferProducts(prms).subscribe(
                res => {
                    this.config.scrollToTop(0, 300);
                    if ( res['data'] && res['data'].length ) {
                        this.result = res.data;
						this.dataService.sendReviews(this.result[0]);
                        this.result[0]['name']  		= this.resultSlide[this.activeSlide]['name'];
                        this.result[0]['skuCode']  		= this.resultSlide[this.activeSlide]['skuCode'];
                        this.result[0]['refillCode']  	= this.resultSlide[this.activeSlide]['refillCode'];
                        this.result[0]['comboCode']  	= this.resultSlide[this.activeSlide]['comboCode'];
                        this.resultMsg = '';
                    } else {
                        this.productType = oldProduct;
                    }
                },
                (err: HttpErrorResponse) => {
                    if (err.error instanceof Error) {
                        console.log('Client Error: ' + err.error.message);
                    } else {
                        console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
                    }
                }
            );
        }
    }
}

