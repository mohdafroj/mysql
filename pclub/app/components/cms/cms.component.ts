import { Component, OnInit, ElementRef, ViewEncapsulation } 						from '@angular/core';
import { NgStyle } 												from '@angular/common';
import { Title, Meta, MetaDefinition, DomSanitizer } 			from '@angular/platform-browser';
import { Router, ActivatedRoute, NavigationEnd } 				from '@angular/router';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { ToastrService } 										from 'ngx-toastr';
import { FormGroup, FormControl, Validators } 					from '@angular/forms';
import { Myconfig } 											from './../../_services/pb/myconfig';
import { ProductsService } 										from './../../_services/pb/products.service';
import { CustomerService } 										from './../../_services/pb/customer.service';
import { StoreService } 										from './../../_services/pb/store.service';
import { TrackingService } 										from './../../_services/tracking.service';
import { SeoService } 											from './../../_services/seo.service';
import { DataService } 											from './../../_services/data.service';

@Component({
  selector: 'pc-cms',
  templateUrl: './cms.component.html',
  styleUrls: ['./cms.component.css'],
  encapsulation:ViewEncapsulation.None
})
export class CmsComponent implements OnInit {
	pageType = 'loading';
	plusContent = [];
	productOffer = '';
	pincodeForm:FormGroup;
	userId:number							= 0;
	result	:any							= {};
	resultMsg:string						= '';
	resultStatus:number						= 0;
	urlKey									= '';	
	productId:number						= 0;	
	nguSecond;
	nguSecondToken:string;
	customerCart = [];
	serverRequest: boolean = true;
	pincodeMessage = '';
	pincodeStep = 0;
	sliderIndex = 0;
	toggleIndex = 0;
	toggleActive = 'minus';
	toggleClass = ['minus','plus','plus','plus','plus','plus','plus','plus','plus','plus','plus'];
	
	newReview = [];
	reviewSelected = 0;
	reviewMsg	= '';
	reviewStatus = 0;
	reviewForm: FormGroup;
	customerReviews = 0;
	customerRating = 0;
	reviewsList = [];
	reviewsPage = 1;
	reviewsLoader = '';
	reviewOrder = 0;
	moreReviewsFlag = 1;
	
	sanitizer:any;
	constructor(
		private toastr:ToastrService, 
		private meta:Meta, 
		private title: Title, 
		private router: Router, 
		private route: ActivatedRoute, 
		private product: ProductsService, 
		private customer: CustomerService, 
		private store: StoreService, 
		private config:Myconfig, 
		private elem:ElementRef, 
		private track:TrackingService,
		private seo: SeoService,
		private dataService: DataService,
		private sanitize:DomSanitizer
	) {
		this.sanitizer = sanitize;
		this.newReview = [
			{
				rate: 1,
				title: 'Sick',
				color:'rgb(59, 78, 118)',
				selected: 0
			},
			{
				rate: 2,
				title: 'Bad',
				color:'rgb(59, 78, 118)',
				selected: 0
			},
			{
				rate: 3,
				title: 'Okay',
				color:'rgb(59, 78, 118)',
				selected: 0
			},
			{
				rate: 4,
				title: 'Good',
				color:'rgb(59, 78, 118)',
				selected: 0
			},
			{
				rate: 5,
				title: 'Great',
				color:'rgb(59, 78, 118)',
				selected: 0
			}
		];
	}

    ngOnInit() {
		this.userId = this.customer.getId();
		let pin: any = localStorage.getItem('savePincode');
		let myCart = this.customer.getFromCart();
		this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
		let savePincode = '';
		if ( pin != 'undefined' ){
			savePincode = JSON.parse(pin);
		}
		this.pincodeForm = new FormGroup ({
			pincode: new FormControl(savePincode, Validators.compose([Validators.required]) )
		});
		this.reviewForm = new FormGroup ({
			title: new FormControl('', Validators.compose([Validators.required]) ),
			description: new FormControl('', Validators.compose([Validators.required]) )
		});
		this.route.paramMap.subscribe(res => {
			this.urlKey = res.get('key');  
			this.title.setTitle(this.urlKey);
			this.config.scrollToTop();
			this.getDetails();
		});
				
		this.nguSecond = {
			grid: {xs: 1, sm: 1, md: 1, lg: 1, all: 0},
			slide: 1,
			speed: 2000,
			interval: 2000,
			point: {
				visible: true,
				hideOnSingleSlide:true,
				pointStyles: `
					.ngucarouselPoint{
						margin-top:10px;
						padding-left: 0px;
						text-align:center;
					}
					.ngucarouselPoint li {
						display: inline-block;
						border-radius: 50%;
						background: #000000;
						padding: 4px;
						margin: 0 3px;
					}
					.ngucarouselPoint li.active {
						background: #38b8bf;
						transform: scale(1.1);
					}
					`
			},
			load: 1,
			touch: true,
			loop: true,
			easing: 'ease'
		};
		
		this.result = {
			id: 0,
			name: '',
			title: '',
			sku: '',
			urlKey: '',
			size: 0,
			sizeUnit: '',
			price: 0,
			isStock: false,
			shortDescription: '',
			description: '',
			metaTitle: '',
			metaKeyword: '',
			metaDescription: '',
			categories: [],
			brand: {},
			images: [],
			notes: [],
			related: [],
			reviews: [],
			custReviews: {customers:0,rating:0},
			progressRating: []
		};	  
  	}
  
  	getDetails(){
		this.resultMsg = 'Loading...';
		this.resultStatus = 0;
		let prms = new HttpParams();
		prms = prms.append('key', this.urlKey);
		prms = prms.append('userId', `${this.userId}`);
		this.product.getPages(prms).subscribe(
		  res => { 
			if( res.status ){
				this.pageType  = res.data.pageType;
				this.result = res.data;
				this.plusContent = this.result.plusContent;
				this.productOffer = this.result.offer;
				//this.data.sendReviews(this.result);
				this.reviewsList = (this.result['reviews'] == undefined) ? [] : this.result.reviews;
				//this.data.sendRelatedProduct({userId: this.userId, items: this.result.related});
				this.title.setTitle(this.result.title);
				this.resultMsg = '';
				if( (this.result.metaTitle != undefined) && (this.result.metaTitle != null) && (this.result.metaTitle != '') ){
					this.title.setTitle(this.result.metaTitle);
				}
				if(this.result.metaKeyword != ''){
					const keyword: MetaDefinition = { name: 'keywords', content: this.result.metaKeyword};
					this.meta.addTag(keyword);
				}
				if(this.result.metaDescription != ''){
					const description: MetaDefinition = { name: 'description', content: this.result.metaDescription};
					this.meta.addTag(description);
				}
				switch( this.pageType ){
					case 'product':
						this.customerReviews = this.result['custReviews']['customers'];
						this.customerRating = this.result['custReviews']['rating'];
						this.seo.ogMetaTag(this.result.metaTitle, this.result.metaDescription, this.result.images[0].large);
						this.track.clickProduct(this.result);
						this.seo.removeAMPPageLink();
						break;
					case 'static':
						if( res.data.is_amp ){
							this.seo.createAMPPageLink();
						}
						this.seo.ogMetaTag(this.result.metaTitle, this.result.metaDescription, res.data.image);
						break;
					default:
				}
			}else{
				this.pageType  = 'notfound';
				this.resultMsg = res.message;
			}
			this.resultStatus = 1;
			
		  },
		  (err: HttpErrorResponse) => {
			if(err.error instanceof Error){
			  this.resultMsg = err.error.message;
			}else{
			  this.resultMsg = err.error;
			}
			this.resultStatus = 1;
		  }
		);
	}

	checkPincode (pincode) {
		this.pincodeMessage = 'Waiting...';
		//let pincode = this.elem.nativeElement.querySelector('#pincode').value;
		if( pincode > 0 ){
			this.pincodeStep = 1;
			this.store.checkPincode(pincode).subscribe(
				res => {
					this.pincodeMessage = res.message;
					if( res.status ){
						localStorage.setItem('savePincode', JSON.stringify(res.data.pincode));
						this.pincodeStep = 2;
					} else {
						this.pincodeStep = 0;
					}
				},
				(err: HttpErrorResponse) => {
					this.pincodeMessage = 'Sorry, there are some app issue!';
					this.pincodeStep = 0;
				}
			);
		} else {
			this.pincodeStep = 0;
			this.pincodeMessage = 'Please enter pincode number!';
		}
	}

	toggleMethod (index) {
		this.toggleActive = (this.toggleClass[index] == 'minus') ? 'plus' : 'minus';
		this.toggleClass = ['plus','plus','plus','plus','plus','plus','plus','plus','plus','plus','plus'];
		this.toggleClass[index] = this.toggleActive;
		this.toggleIndex = index;
	}

	addCart(item){
		let myCart = this.customer.getFromCart();
		if( this.userId > 0 ){
			let carItemIds = myCart['shopping']['cart'].map( (item) => { return item.id; });
			let carItemQuantities = myCart['shopping']['cart'].map( (item) => { return item.cart_quantity; });
			carItemIds.push(item.id);
			carItemQuantities.push(1);
			carItemIds = carItemIds.join(',');
			carItemQuantities = carItemQuantities.join(',');
			let formData = {itemId: carItemIds, quantity: carItemQuantities, useraction: 'add'};
			this.store.addToCart(formData).subscribe(
				res => {
					if ( res.data.cart ) { this.customer.setCart(res.data.cart); }
					if( res.status ){
						this.toastr.success(res.message);
						myCart = this.customer.getFromCart();
						this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
						this.track.addToCart(item);
					} else {
						this.toastr.error(res.message);
					}
				},
				(err: HttpErrorResponse) => {
					this.toastr.error("Sorry, there are some app issue!");
				}
			);				
		} else {
			//localStorage.setItem('productId', item.id);
			//this.router.navigate(['/registration']);
			let shoppingObject = {
				brand: item.brand,
				cart_id: 0,
				cart_quantity: 1,
				categories: item.categories,
				description: item.shortDescription,
				discount: item.discount,
				gender: item.gender,
				id: item.id,
				images: [{id: 0, product_id: item.id, title: item.title, alt: item.title, url: (item.images[0]['large'] == undefined) ? '' : item.images[0]['large'] }],
				name: item.name,
				price: item.price,
				price_logo: item.priceLogo,
				size: item.size,
				sku: item.sku,
				title: item.title,
				unit: item.unit,
				url_key: item.urlKey
				
			};
			myCart['shopping']['cart'].push(shoppingObject);
			this.customer.setCart(myCart['shopping']['cart']);
			this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
			this.toastr.success("One item added into cart!");			
		}
	}
	
	getReviews(page) {
		this.reviewsLoader = 'Loading...';
		let param = {productId: this.result.id, page:page, order: this.reviewOrder };
		this.product.getProductReviews(param).subscribe(
            res => {
				if(res.data.total > 0){
					if ( this.reviewsPage == 1 ) {
						this.reviewsList = [];
					}
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
	
	reviewSorting(e) {
		this.reviewOrder = e.target.value;
		this.reviewsPage = 1;
		this.getReviews(this.reviewsPage);
	}
	
	selectRating(review) {
		this.reviewSelected = review;
		this.newReview.forEach(function(item){
			if ( item.rate == review ) { 
				item.selected = 1;
				item.color = 'rgb(238, 149, 145)';
			} else {
				item.selected = 0;
				item.color = 'rgb(59, 78, 118)';
			}
		}, review);
	}
	
	addReview(formData){
		this.reviewStatus = 1;
		this.reviewMsg = 'Wait...'
		formData.itemId = this.result.id;
		formData.rating = this.reviewSelected;
		if( this.userId > 0 ){
			this.customer.addReviews(formData).subscribe(
				res => {
					if(res.status){
						this.reviewStatus = 2;
						this.reviewForm = new FormGroup ({
							title: new FormControl('', Validators.compose([Validators.required]) ),
							description: new FormControl('', Validators.compose([Validators.required]) )
						});
						this.reviewSelected = 0;
					}
					this.reviewMsg = res.message;
				},
				(err: HttpErrorResponse) => {
					this.reviewMsg = "Sorry, there are some app issue!";
				}
			);
		}else{
			this.router.navigate(['/customer/login'], {queryParams:{}});
		}
	} 
}
