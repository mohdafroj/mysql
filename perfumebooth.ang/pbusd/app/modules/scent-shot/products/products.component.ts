import { Component, OnInit, ElementRef, HostListener,ViewChild } 			from '@angular/core';
import { FormGroup, FormControl, Validators } 					from '@angular/forms';
import { Title, Meta, MetaDefinition,DomSanitizer } 			from '@angular/platform-browser';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { Router, ActivatedRoute, NavigationEnd, Params } 		from '@angular/router';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { ProductsService } 										from './../../../_services/pb/products.service';
import { CustomerService } 										from '../../../_services/pb/customer.service';
import { StoreService } 										from './../../../_services/pb/store.service';
import { TrackingService } 										from './../../../_services/tracking.service';
import { DataService } 										from './../../../_services/data.service';
import { NguCarousel, NguCarouselConfig, NguCarouselStore } 	from '@ngu/carousel';
import { ToastrService } 										from 'ngx-toastr';

@Component({
  selector: 'app-products',
  templateUrl: './products.component.html',
  styles: [`
  		.kit_contains_div p{color:rgba(0,0,0,0.7); font-size:16px; padding:0 0 2px 0; line-height:20px; margin:0}
  `],
  styleUrls: ['./products.component.css'],
})
export class ProductsComponent implements OnInit {
	productId:number				=0;
	
    resultSlide:any					=[];
	activeSlide:number              =0;
    result:any						=[];
	productType:string				='product';
	resultMsg:string				='';
    userId:number					=0;

	category:number					= 7;
	gender:string					= '';
	queryParameter:any				= {};
	topScrollClass:string 	= 'affix-top';

	nguFirst: NguCarouselConfig;
	nguSecond: NguCarouselConfig;	
	nguThird: NguCarouselConfig;
	
	nguFirstToken:string;
	nguSecondToken:string;
	nguThirdToken:string;
	
	@ViewChild('nguCarouselFirst') nguCarouselFirst: NguCarousel<any>;
	@ViewChild('nguCarouselSecond') nguCarouselSecond: NguCarousel<any>;	
	@ViewChild('nguCarouselThird') nguCarouselThird: NguCarousel<any>;
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
		private dataService: DataService,
		private track:TrackingService 
	){
		this.sanitizer = sanitize;
    }
    
	ngOnInit() {
		this.userId = this.customer.getId();
		this.config.scrollToTop();
		this.title.setTitle('Buy Perfume Scent shot || Best Niche Perfume Brands');
		let keyword: MetaDefinition = { name: 'keywords', content: 'perfume scent shot, scent shot, luxury perfume, international brands perfumes, scent shot refill pack, perfume for men, perfume for girls'};
		this.meta.addTag(keyword);
		let description: MetaDefinition = { name: 'description', content: 'Browse luxury choice fragrance of top international brands and order your dream perfume at low price. We provide doorstep delivery in India.'};
		this.meta.addTag(description);
		
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
			grid: {xs: 3, sm: 6, md: 6, lg: 6, all: 0},
			slide: 3,
			speed: 400,
			point: {
				visible: true,
				hideOnSingleSlide:true
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
				timing:2000,
				initialDelay:1000
			},
			point: {
				visible: true,
				hideOnSingleSlide:true
			},
			load: 1,
			touch: true,
			loop: true,
			easing: 'ease'
		};
		this.nguThird = {
			grid: {xs: 1, sm: 1, md: 1, lg: 1, all: 0},
			slide: 1,
			speed: 1000,
			interval: {
				timing:2000,
				initialDelay:1000
			},
			point: {
				visible: false,
				hideOnSingleSlide:true
			},
			load: 1,
			touch: true,
			easing: 'ease'
		};
	}
	
	initDataFirstFn(key: NguCarouselStore){
		this.nguFirstToken = key.token;
	}

	initDataSecondFn(key: NguCarouselStore){
		this.nguSecondToken = key.token;
	}

	initDataThirdFn(key: NguCarouselStore){
		this.nguThirdToken = key.token;
		//console.log(key);
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
		
		this.products.getOfferProducts(prms).subscribe(
            res => { //console.log(res.data);
				this.resultSlide = res.data;
                this.result.push(this.resultSlide[0]);
				this.productId = this.result[0]['id'];
				this.dataService.sendReviews(this.result[0]); //console.log(this.result);
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
			let formData:any = {itemId:itemId,quantity:1};
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
		this.dataService.sendReviews(this.result[0]); //console.log(this.result);
		this.productId 			= this.result[0]['id']
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
	
	productPopup(index, pIndex){
		this.dataService.sendPopupProduct({index: index, items: this.result[pIndex]['related']});
		return false;
	}
	
	subProduct(sku:string, code:string){
		let oldProduct = this.productType;
		
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		if( code == 'refill' ){
			prms = prms.append('category-id', '4'); //perfume selfie category id
		}else{
			prms = prms.append('category-id', `${this.category}`);
		}
		prms = prms.append('sku_code', sku);
		if( code == 'combo' ){
			prms = prms.append('combo', '1');
		}
		if( code != this.productType ){
			this.productType = code;
			this.products.getOfferProducts(prms).subscribe(
				res => {
					this.config.scrollToTop(0, 300);
					if( res['data'] && res['data'].length ){
						this.result = res.data;
						this.productId = this.result[0]['id'];
						this.result[0]['name']  		= this.resultSlide[this.activeSlide]['name'];
						this.result[0]['skuCode']  		= this.resultSlide[this.activeSlide]['skuCode'];
						this.result[0]['refillCode']  	= this.resultSlide[this.activeSlide]['refillCode'];
						this.result[0]['comboCode']  	= this.resultSlide[this.activeSlide]['comboCode'];
						this.dataService.sendReviews(this.result[0]); //console.log(this.result);
						this.resultMsg = '';
					}else{
						this.productType = oldProduct;
					}
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

