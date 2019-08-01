import { Component, OnInit, ElementRef, HostListener, ViewChild } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Title, Meta, MetaDefinition, DomSanitizer } from '@angular/platform-browser';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Router, ActivatedRoute, NavigationEnd, Params } from '@angular/router';
import { Myconfig } from './../../../_services/pb/myconfig';
import { CustomerService } from '../../../_services/pb/customer.service';
import { ProductsService } from './../../../_services/pb/products.service';
import { StoreService } from './../../../_services/pb/store.service';
import { TrackingService } from './../../../_services/tracking.service';
import { SeoService } from './../../../_services/seo.service';
import { DataService } from './../../../_services/data.service';

import { NguCarousel, NguCarouselConfig, NguCarouselStore } from '@ngu/carousel';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-perfume-selfie',
  templateUrl: './perfume-selfie.component.html',
  styleUrls: [
    './perfume-selfie.component.css'
  ]
})
export class PerfumeSelfieComponent implements OnInit {
    resultSlide: any					= [];
    activeSlide              = 0;
    result: any						= [];
    resultMsg 				= '';
    userId = 0;

    category					= 6;
    gender					= '';
    queryParameter: any				= {};
    topScrollClass 	= 'affix-top';

    nguFirst: NguCarouselConfig;
    nguSecond: NguCarouselConfig;

    nguFirstToken: string;
    nguSecondToken: string;

    @ViewChild('nguCarouselFirst') nguCarouselFirst: NguCarousel<any>;
    @ViewChild('nguCarouselSecond') nguCarouselSecond: NguCarousel<any>;
    sanitizer: any;
    constructor (
        private toastr: ToastrService,
        private elem: ElementRef,
        private meta: Meta,
        private title: Title,
        private products: ProductsService,
        private router: Router,
        private route: ActivatedRoute,
        private auth: CustomerService,
        private store: StoreService,
        private config: Myconfig,
        private track: TrackingService,
        private sanitize: DomSanitizer,
        private seo: SeoService,
		private dataService: DataService
    ) {
        this.sanitizer = sanitize;
    }

    ngOnInit() {
        this.userId = this.auth.getId();
        this.config.scrollToTop();
        const title = 'Online Perfume Selfie';
        const metaDescription = `
			Buy perfume selfie box for girls, women and men at offer price.
			Each perfume selfie box contains seven different new fragrance of International
			Brands like Lomani, Emper, Maryaj, English Blazer, Chris Adams,  Baug Sons, Louis Cardin and many more.`;
        this.title.setTitle(title);
        const keyword: MetaDefinition = {
            name: 'keywords',
            content: 'perfume, perfume for men, perfume for women, perfumes, international brand perfumes'};
        this.meta.addTag(keyword);
        const description: MetaDefinition = { name: 'description', content: metaDescription };
        this.meta.addTag(description);
        this.seo.ogMetaTag(
			'Online Perfume Selfie',
			'Buy perfume selfie box for girls, women and men at offer price. Each perfume selfie box contains seven different new fragrance of International Brands like Lomani, Emper, Maryaj, English Blazer, Chris Adams,  Baug Sons, Louis Cardin and many more.',
			'https://www.perfumebooth.com/assets/images/selfie_images/selfie_product/img_3.jpg'
		);

        this.route.queryParams.subscribe((params: Params) => {
            this.queryParameter = params;
            switch (params.gender) {
                case 'male': this.gender = params.gender; break;
                case 'female': this.gender = params.gender; break;
                default: this.gender = '';
            }
            this.getPerfumeSelfie();
        });

        this.nguFirst = {
            grid: {xs: 3, sm: 6, md: 6, lg: 6, all: 0},
            slide: 3,
            speed: 400,
            point: {
                visible: true,
                hideOnSingleSlide: true,
            },
            load: 1,
            touch: true,
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
            touch: true,
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
    getPerfumeSelfie() {
        this.resultMsg = 'loading...';
        this.result = [];
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
            res => { // console.log(res.data);
                this.resultSlide = res.data;
                this.result.push(this.resultSlide[0]);
				this.dataService.sendReviews(this.result[0]);
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
                    this.auth.setCart(res.data.cart);
                    for (const i of this.resultSlide) {
                        if ( itemId === i.id ) { i.isCart = 1; }
                    }
                    for ( const i of this.result) {
                        if ( itemId === i.id ) { i.isCart = 1; }
                    }
                    this.toastr.success(res.message);
                    const myCart: any = this.auth.getCart();
                    for (let i = 0; i < myCart.length; i++) {
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
        this.activeSlide 		= index;
        this.result.push(this.resultSlide[this.activeSlide]);
		this.dataService.sendReviews(this.result[0]);
        return false;
    }

    selectGender(gen: string) {
        if ( gen !== this.gender ) {
            // this.nguCarouselFirst.reset(true);
            this.gender = gen;
            this.getPerfumeSelfie();
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

    @HostListener('window:scroll') checkScroll() {
        const scrollPosition: number = window.pageYOffset;
        if ( scrollPosition > 400 ) {
            this.topScrollClass = 'affix';
        } else {
            this.topScrollClass = 'affix-top';
        }
    }
}
