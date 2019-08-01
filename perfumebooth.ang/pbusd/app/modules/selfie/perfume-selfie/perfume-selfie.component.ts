import { Component, OnInit, ElementRef, HostListener, ViewChild } from '@angular/core';
import { FormGroup, FormControl, Validators } 					from '@angular/forms';
import { Title, Meta, MetaDefinition,DomSanitizer } 			from '@angular/platform-browser';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { Router, ActivatedRoute, NavigationEnd, Params } 		from '@angular/router';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { CustomerService } 										from '../../../_services/pb/customer.service';
import { ProductsService } 										from './../../../_services/pb/products.service';
import { StoreService } 										from './../../../_services/pb/store.service';
import { TrackingService } 										from './../../../_services/tracking.service';
import { NguCarousel, NguCarouselConfig, NguCarouselStore } 	from '@ngu/carousel';
import { ToastrService } 										from 'ngx-toastr';

@Component({
  selector: 'app-perfume-selfie',
  templateUrl: './perfume-selfie.component.html',
  styleUrls: [
	'./perfume-selfie.component.css'
  ]
})
export class PerfumeSelfieComponent implements OnInit {
	rForm:FormGroup;
	reviewMsg:string				='';
	reviewSelected:any				= [1,2,3,4,5];
	reviewsList:any					= [];
	reviewRemain:any				= [];
	reviewsLoader:string			='';
	moreReviewsFlag:number   		=1;
	reviewsPage:number				=1;
	productId:number				=0;
	notifymeMsg:string				= '';
	notifymeClass:string			= '';
	
    resultSlide:any					=[];
	activeSlide:number              =0;
    result:any						=[];
	resultMsg:string				='';
	popupResult:any 				=[];	
    userId:number					=0;
	
	category:number					= 6;
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
		private toastr:ToastrService, 
		private elem:ElementRef, 
		private meta:Meta, 
		private title: Title, 
		private products: ProductsService, 
		private router: Router, 
		private route: ActivatedRoute, 
		private auth: CustomerService, 
		private store: StoreService, 
		private config:Myconfig, 
		private track:TrackingService,
		private sanitize: DomSanitizer
	) {
		this.sanitizer = sanitize;
    }
    
	ngOnInit() {
		this.userId = this.auth.getId();
		this.config.scrollToTop();
		this.rForm = new FormGroup ({
			title: new FormControl("", Validators.compose([Validators.required]) ),
			description: new FormControl("", Validators.compose([Validators.required]) )
		});
        
		this.title.setTitle('Online Perfume Selfie');
		let keyword: MetaDefinition = { name: 'keywords', content: 'perfume, perfume for men, perfume for women, perfumes, international brand perfumes'};
		this.meta.addTag(keyword);
		let description: MetaDefinition = { name: 'description', content: 'Buy perfume selfie box for girls, women and men at offer price. Each perfume selfie box contains seven different new fragrance of International Brands like Lomani, Emper, Maryaj, English Blazer, Chris Adams,  Baug Sons, Louis Cardin and many more.'};
		this.meta.addTag(description);
		
		this.route.queryParams.subscribe((params: Params) => {
			this.queryParameter = params;
            switch(params.gender){
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
				hideOnSingleSlide:true,
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
	}

	getPerfumeSelfie(){
		this.resultMsg = 'loading...';
		this.result = [];
		this.reviewsList	= [];
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
				this.reviewsPage = 1;
				this.getReviews(this.reviewsPage);				
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
					this.auth.setCart(res.data.cart);
					for(let i of this.resultSlide){
						if( itemId == i.id ){ i.isCart = 1; }
					}
					for(let i of this.result){
						if( itemId == i.id ){ i.isCart = 1; }
					}
					this.toastr.success(res.message);
					let myCart:any = this.auth.getCart();
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
	  
	popupResults(index:number, rIndex:number){
		//this.nguCarouselThird.reset(true);
		//this.nguCarouselThird.moveTo(rIndex);
		this.popupResult = [];
		this.popupResult = this.result[index]['related'];
		//console.log(event);
		return false;
	}
	
	resultSlides(index:number){
		//this.nguCarouselSecond.reset(true);
		this.result 			= [];
		this.activeSlide 		= index;
		this.reviewsList 		= [];
		this.reviewsPage 		= 1;
		this.moreReviewsFlag 	= 1;
		this.result.push(this.resultSlide[this.activeSlide]);
		this.productId 			= this.result[0]['id']
		this.getReviews(this.reviewsPage);
		return false;
	}
	
	selectGender(gen:string){
		if( gen != this.gender ){
			//this.nguCarouselFirst.reset(true);	
			this.gender = gen;
			this.getPerfumeSelfie();
		}
	}
	
	getFinalRating(num){
		return this.config.numToArray(num);
	}
	getRemainingRating(num){
		let a = 5 - num;
		return this.config.numToArray(a);
	}
	
	selectReview(n){
		switch(n){
			case 1: this.reviewSelected = [1]; this.reviewRemain = [1,2,3,4]; break;
			case 2: this.reviewSelected = [1,2]; this.reviewRemain = [1,2,3]; break;
			case 3: this.reviewSelected = [1,2,3]; this.reviewRemain = [1,2]; break;
			case 4: this.reviewSelected = [1,2,3,4]; this.reviewRemain = [1]; break;
			default: this.reviewSelected = [1,2,3,4,5]; this.reviewRemain = [];
		}
	}
	
	unselectReview(n){
		n = n + this.reviewSelected.length;
		switch(n){
			case 1: this.reviewSelected = [1]; this.reviewRemain = [1,2,3,4]; break;
			case 2: this.reviewSelected = [1,2]; this.reviewRemain = [1,2,3]; break;
			case 3: this.reviewSelected = [1,2,3]; this.reviewRemain = [1,2]; break;
			case 4: this.reviewSelected = [1,2,3,4]; this.reviewRemain = [1]; break;
			default: this.reviewSelected = [1,2,3,4,5]; this.reviewRemain = [];
		}
	}
	
	getReviews(page){
		this.reviewsLoader = 'Loading...';
		this.products.getProductReviews(this.productId, page).subscribe(
            res => {
				if(res.data.total > 0){
					for( let i=0; i<res.data.total; i++ ){
						this.reviewsList.push(res.data.reviews[i]);
					}					
				}
				this.moreReviewsFlag = res.data.viewMore;
				this.reviewsLoader   = '';
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
	
	loadMoreReviews(){
		this.reviewsPage = this.reviewsPage + 1;
		this.getReviews(this.reviewsPage);
	}
	
	addReview(formData){
		this.reviewMsg = 'Wait...'
		formData.itemId = this.productId;
		formData.rating = this.reviewSelected.length;
		if( this.userId > 0 ){
			//console.log(formData);
			this.auth.addReviews(formData).subscribe(
				res => {
					if(res.status){
						this.rForm = new FormGroup ({
							title: new FormControl("", Validators.compose([Validators.required]) ),
							description: new FormControl("", Validators.compose([Validators.required]) )
						});
						this.reviewSelected = [1,2,3,4,5];
						this.reviewRemain = [];
					}
					this.reviewMsg = res.message;
				},
				(err: HttpErrorResponse) => {
					this.reviewMsg = "Sorry, there are some app issue!";
				}
			);
		}else{
			//this.auth.getInActive();
			this.router.navigate(['/customer/login'], {queryParams:{}});
		}
	}

	notifyMePopup(itemId){
		this.productId = itemId;
		this.notifymeClass = '';
		this.notifymeMsg = '';
	}
	
	notifyMeSubmit(){
		this.notifymeClass = 'loader_msz';
		var email = this.elem.nativeElement.querySelector('#notifyemail').value;
		if( this.config.EMAIL_REGEXP.test(email) ){
			let formData:any = {productId:this.productId,email:email};
			this.products.notifyMe(formData).subscribe(
				res => {
					if( res.status ){
						this.notifymeClass = 'success_msz';
					}else{
						this.notifymeClass = 'error_msz';
					}
					this.notifymeMsg = res.message;
				},
				(err: HttpErrorResponse) => {
					this.notifymeClass = 'error_msz';
					this.notifymeMsg = 'Sorry, there are some app issue!';
				}
			);
		}else{
			this.notifymeClass = 'error_msz';
			this.notifymeMsg = 'Please enter valid email id!';
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
