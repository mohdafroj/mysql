import { Component, OnInit, ElementRef } 						from '@angular/core';
import { NgStyle } 												from '@angular/common';
import { Title, Meta, MetaDefinition, DomSanitizer } 			from '@angular/platform-browser';
import { FormGroup, FormControl, Validators } 					from '@angular/forms';
import { Router, ActivatedRoute, NavigationEnd } 				from '@angular/router';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { ProductsService } 										from './../../../_services/pb/products.service';
import { CustomerService } 										from '../../../_services/pb/customer.service';
import { StoreService } 										from './../../../_services/pb/store.service';
import { TrackingService } 										from './../../../_services/tracking.service';
import { SeoService } 											from './../../../_services/seo.service';
import { DataService } 											from './../../../_services/data.service';

import { NguCarousel, NguCarouselStore } 	from '@ngu/carousel';
import { ToastrService } 										from 'ngx-toastr';

@Component({
  selector: 'app-details',
  templateUrl: './details.component.html',
  styleUrls: [
	'./details.component.css'
  ]
})
export class DetailsComponent implements OnInit {
	userId:number							= 0;
	result:any								= [];
	resultMsg:string						= '';
	resultStatus:number						= 0;
	urlKey:string							= '';
	savePincode:any						    = '';
	pincodeClass:string						= 'pincode_error';
	pincodeMsg:string						= '';
	
	nguSecond;
	nguSecondToken:string;
	
	sanitizer:any;
	
	constructor(
		private toastr:ToastrService, 
		private meta:Meta, 
		private title: Title, 
		private router: Router, 
		private route: ActivatedRoute, 
		private products: ProductsService, 
		private auth: CustomerService,
		private store: StoreService, 
		private config:Myconfig, 
		private elem:ElementRef, 
		private track:TrackingService,
		private seo:SeoService,
		private sanitize:DomSanitizer,
		private dataService: DataService
	) {
		this.sanitizer = sanitize
	}
	
	ngOnInit() {
		this.userId = this.auth.getId();
		let pin: any = localStorage.getItem('savePincode');
		if ( pin != 'undefined' ){
			this.savePincode = JSON.parse(pin);
		}
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
			loop: false,
			easing: 'ease'
		};
		
		this.result = {
			id:0,
			name:'',
			title:'',
			skuCode:'',
			urlKey:'',
			size:0,
			sizeUnit:'',
			price:0,
			isStock:false,
			shortDescription:'',
			description:'',
			metaTitle:'',
			metaKeyword:'',
			metaDescription:'',
			categories:[],
			brand:{},
			images:[],
			notes:[],
			related:[],
			reviews:[],
			custReviews:{customers:0,rating:0},
			paymentOffer:{},
			progressRating:[]
		};		
		
	}
	
	initDataSecondFn(key: NguCarouselStore){
		this.nguSecondToken = key.token;
	}

	getDetails(){
		this.resultMsg = 'Loading...';
		this.resultStatus = 0;
		let prms = new HttpParams();
		prms = prms.append('key', this.urlKey);
		prms = prms.append('userId', `${this.userId}`);
		this.products.getDetails(prms).subscribe(
		  res => {
			if( res.status ){
				this.result = res.data; //console.log(this.result);
				this.title.setTitle(this.result.title);
				this.resultMsg = '';
				this.dataService.sendReviews(this.result);
				this.dataService.sendRelatedProduct({userId: this.userId, items: this.result.related});
				if(this.result.metaTitle != ''){
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
				this.seo.ogMetaTag(this.result.metaTitle, this.result.metaDescription, this.result.images[0].imgLarge)
				this.track.trackProductClick(this.result);
			}else{
				this.router.navigate(['/'], {queryParams:{}});
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

	getFinalRating(num:number):any{
		return this.config.numToArray(num);
	}
	
	getRemainingRating(num){
		let a = 5 - num;
		return this.config.numToArray(a);
	}

	addToWishlist(itemId){
		if( this.userId > 0 ){
			let formData:any = {itemId:itemId};
			if( this.result.isWishlist == 0 ){
				this.auth.addToWishlist(formData).subscribe(
					res => {
						if( res.status ){
							this.result.isWishlist = 1;
							this.toastr.success(res.message);
						}else{
							this.result.isWishlist = 0;
							this.toastr.error(res.message);
						}
					},
					(err: HttpErrorResponse) => {
						if(err.error instanceof Error){
							this.toastr.error('Client: '+err.error.message);
						}else{
							this.toastr.error('Client: '+JSON.stringify(err.error));
						}
					}
				);
			}else{
				this.auth.updateWishlist(formData).subscribe(
				  res => {
					if(res.status){
						this.result.isWishlist = 0;
						this.toastr.success(res.message);
					}else{
						this.result.isWishlist = 1;
						this.toastr.error(res.message);
					}
				  },
				  (err: HttpErrorResponse) => {
					if(err.error instanceof Error){
						this.toastr.error('Client: '+err.error.message);
					}else{
						this.toastr.error('Client: '+JSON.stringify(err.error));
					}
				  }
				);
			}
		}else{
			this.router.navigate(['/customer/login']);
		}
	}

	shareAndEarn(){
		if( this.userId > 0 ){
			this.router.navigate(['/customer/share-and-earn'], {});
		}else{
			this.router.navigate(['/customer/login']);
		}
	}

	addCart(itemId){
		localStorage.setItem('productId', itemId);
		if( this.userId > 0 ){
			let formData:any = {itemId:itemId,qty:1};
			this.store.addToCart(formData).subscribe(
				res => {
					if( res.status ){
						this.auth.setCart(res.data.cart);
						if( itemId == this.result.id ){ this.result.isCart = 1; }		
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
	
	goToCart(){ this.router.navigate(['/checkout/cart'], {}); }

	checkPincode(){
		this.pincodeClass='pincode_error';
		this.pincodeMsg = 'Waiting...';
		let pincode:number;
		pincode = this.elem.nativeElement.querySelector('#pincode').value;
		if(pincode > 0){
			this.store.checkPincode(pincode).subscribe(
				res => {
					this.pincodeMsg = res.message;
					if( res.status ){
						this.pincodeClass='pincode_success';
						localStorage.setItem('savePincode', JSON.stringify(res.data.pincode));
					}
				},
				(err: HttpErrorResponse) => {
					this.pincodeMsg = 'Sorry, there are some app issue!';
				}
			);
		}else{
			this.pincodeMsg = 'Please enter pincode number!';
		}
	}
  
	productPopup(index){
		this.dataService.sendPopupProduct({index: index, items: this.result.related});
		return false;
	}
	
	notifyMePopup(itemId){
		this.dataService.sendNotifyme({userId: this.userId, productId: itemId});
	}
	
}
