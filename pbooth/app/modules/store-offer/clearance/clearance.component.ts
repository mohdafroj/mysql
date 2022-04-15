import { Component, OnInit,ViewChild,ElementRef,HostListener } 	from '@angular/core';
import { Title, Meta, MetaDefinition,DomSanitizer } 			from '@angular/platform-browser';
import { NgStyle } 												from '@angular/common';
import { FormGroup, FormControl, Validators } 					from '@angular/forms';
import { Router, ActivatedRoute, NavigationEnd, Params, NavigationExtras } 		from '@angular/router';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { ProductsService } 										from './../../../_services/pb/products.service';
import { CustomerService } 										from '../../../_services/pb/customer.service';
import { StoreService } 										from './../../../_services/pb/store.service';
import { TrackingService } 										from './../../../_services/tracking.service';
import { DataService } 											from './../../../_services/data.service';
import { ToastrService } 										from 'ngx-toastr';

@Component({
  selector: 'clearance',
  templateUrl: './clearance.component.html',
  styleUrls: ['./clearance.component.css']
})
export class ClearanceComponent implements OnInit {
	saleOn:number			= 0; 
    userId:number			= 0;
    currentPath:string		= '';
	category:string;
	priceMenu = [];
    brandKey:number         = 0;
	selectedPrice:number    = 0;
	gender:string			= '';
	comboStatus:number		= 0;
	page:number				= 1;
	stopPageLoad:number		= 1;
    brands:any				= [];
    productList:any			= [];
	totalProduct:number		= 0;
		
    findTitle:string		= '';
    brandTitle:string		= '';
    currentTitle:string		= '';
	resultMsg:string		= '';
	resultStatus:number		= 0;
	queryParams:any			= [];		
	winWidth:number			= 0;
	
	comboSection:number		= 0;
	
	topScrollClass:string 	= 'affix-top';
	@ViewChild('mainScreen', {static: false}) elementView: ElementRef;
	@ViewChild('filterContainer', {static: false}) filterContainer: ElementRef;
	
	@HostListener('window:load') onLoad() {
		this.winWidth = window.innerWidth;
	}

	@HostListener('window:resize') onResize() {
		this.winWidth = window.innerWidth;
	}

	@HostListener('window:scroll') checkScroll() {
		const scrollPosition:number = window.pageYOffset;
		if( scrollPosition > 400 ){
			this.topScrollClass = 'affix';
		}else{
			this.topScrollClass = 'affix-top';
		}
		const componentPosition:number = this.elem.nativeElement.offsetTop;
		const componentHeight:number = this.elementView.nativeElement.offsetHeight;
		
		if( this.stopPageLoad && this.resultStatus && (this.productList.length > 0) && (scrollPosition > (componentHeight - componentPosition - 900) ) ){
			this.page += 1;
			this.getMoreProducts();
		}
	}

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
		private dataService:DataService
	) {
		this.winWidth = window.innerWidth;
		this.userId = this.auth.getId();
		let urls = route.snapshot.url;
		this.currentPath 	= ( Array.isArray(urls) && urls[0] && urls[0].path ) ? urls[0].path : '';		
		this.currentTitle 	= this.toTitleCase(this.currentPath.replace('-', ' '));			
		this.route.queryParams.subscribe((params: Params) => {
			this.queryParams = params;
			this.gender        = ( params.gender ) ? params.gender : '';
			this.brandKey      = ( params.brand ) ? params.brand : 0;
			this.selectedPrice = ( params.price ) ? params.price : 0;
			this.category      = ( params.category ) ? params.category : 'scent-shot';
			this.getFilterProducts();
		});		
	}
	
	ngOnInit(){
		//console.log(this.winWidth);
	}
	
	ngAfterViewInit(){
		//this.toastr.success('Hello world!');
		this.config.scrollToTop();
	}
    
    getFilterProducts(){
		this.productList	= [];
		this.resultMsg      = 'Loading...';
		this.totalProduct	= 0;
		this.resultStatus   = 0;
		this.page		    = 1;
		this.stopPageLoad   = 1;
		let siteTitle:string = '';
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		prms = prms.append('category', `${this.category}`);
		prms = prms.append('brand', `${this.brandKey}`);
		prms = prms.append('combo', `${this.comboStatus}`);
		prms = prms.append('price', `${this.selectedPrice}`);
		prms = prms.append('gender', `${this.gender}`);
		
		this.products.getStoreOffer(prms, 'products').subscribe(
            res => {
                if(res.status){
					if( res.data.redirect ){ this.router.navigate(['/perfume-bottle/all']); }
					this.saleOn         = res.data.saleOn;
					this.productList 	= res.data.products;
					this.totalProduct	= res.data.total;
					this.brands 		= res.data.brands;
					this.priceMenu 		= res.data.priceMenu;
					siteTitle 			= res.data.siteTitle;					
					this.comboSection   = 0;
					for(let item in this.brands){
						if(this.brands[item].id == this.brandKey){ 
							this.brandTitle = this.brands[item].title;
						}
					}
					if( 0 == this.brandKey){ 
						this.brandTitle = 'All';
					}
					if( this.brands.length == 1 ){
						this.brandKey = this.brands[0].id;
						this.brandTitle = this.brands[0].title;
					}
					for(let item in this.productList){
						if(this.productList[item].isCombo == "1"){ 
							this.comboSection = 1;
						}
					}
				}
				if( this.productList.length < 12 ){
					this.stopPageLoad = 0;
				}
				this.resultMsg = res.message;
				this.title.setTitle(siteTitle);
				this.resultStatus = 1;
            },
            (err: HttpErrorResponse) => {
				this.title.setTitle(siteTitle);
                if(err.error instanceof Error){
					this.resultMsg = err.error.message;
                }else{
					this.resultMsg = JSON.stringify(err.error);
                }
				this.resultStatus = 1;
            }
        );
    }
	
    getMoreProducts(){
		this.resultStatus = 0;
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		prms = prms.append('category', `${this.category}`);
		prms = prms.append('brandKey', `${this.brandKey}`);
		prms = prms.append('combo', `${this.comboStatus}`);
		prms = prms.append('gender', `${this.gender}`);
		prms = prms.append('page', `${this.page}`);
		prms = prms.append('price', `${this.selectedPrice}`);
		
		for (let key in this.queryParams){
		  prms = prms.append(key, this.queryParams[key]);
		}
		
		this.products.getStoreOffer(prms,'products').subscribe(
            res => {
				this.stopPageLoad = 0;
				for(let item in res.data.products){
					if(res.data.products[item].isCombo == "1"){ 
						this.comboSection = 1;
					}
					this.stopPageLoad = 1;
					this.productList.push(res.data.products[item]);
				}
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
	
	addCart(itemId){
		localStorage.setItem('productId', itemId);
		if( this.userId > 0 ){
		  let formData:any = {itemId:itemId,qty:1};
		  //console.log(formData);
		  this.store.addToCart(formData).subscribe(
			res => {
				if( res.status ){
					this.auth.setCart(res.data.cart);
					for(let i of this.productList){
						if( itemId == i.id ){
							if( i.discount.coupon ){
								let cartInfo:any = localStorage.getItem('cartInfo');
								if(cartInfo != null){
									cartInfo = JSON.parse(cartInfo);
									cartInfo.couponCode = i.discount.coupon;
									cartInfo.optionStatus = 4;
								}else{
									cartInfo = {
										couponCode:i.discount.coupon,
										trackPage:'cart',
										optionStatus:4,
										giftVoucherStatus:true,
										pbPointsStatus:false,
										pbCashStatus:true
									};
								}
								localStorage.setItem('cartInfo',JSON.stringify(cartInfo));
							}
							i.isCart = 1;
							break;
						}
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
		  //this.auth.getInActive();
		  this.router.navigate(['/customer/registration']);
		}
		
	}
  
	addToWishlist(itemId){
		if( this.userId > 0 ){
			let formData:any = {itemId:itemId};
			this.auth.addToWishlist(formData).subscribe(
				res => {
					this.toastr.success(res.message);
				},
				(err: HttpErrorResponse) => {
					this.toastr.error("Sorry, there are some app issue!");
				}
			);
		}else{
			this.router.navigate(['/customer/login']);
		}
	}
		
	selectGender(value:string){
		if( value != '' ){			
			this.gender = value;
			let param:any = {};
			if(this.selectedPrice > 0){ param.price = this.selectedPrice; }
			param.gender = this.gender;
			let navigationExtras: NavigationExtras = { queryParams: param };
			this.router.navigate(['/store-offer/', this.currentPath], navigationExtras);
		}
	}
	
	comboSelection(va){
		if( va != this.comboStatus ){
			this.comboStatus = va;
			this.getFilterProducts(); 
		}
	}
	
	goToCart(){ this.router.navigate(['/checkout/cart'], {}); }
	//Category filter
	changeCategory(key){
		this.comboStatus 	= 0;
		this.brandKey 	= 0;
		this.selectedPrice 	= 0;
		this.gender 	= '';
		//this.config.scrollToTop();
		let navigationExtras: NavigationExtras = { queryParams: {category:key} };
		this.router.navigate(['/'+this.currentPath], navigationExtras);		
	}
	//Brand filter
	changeBrand(key){
		this.gender	= '';
		this.comboStatus 	= 0;
		let param:any = {};
		let a:number = this.filterContainer.nativeElement.offsetTop;
		window.scrollTo(0, a+60);
		if(this.category != ''){ param.category = this.category; }
		if(this.selectedPrice > 0){ param.price = this.selectedPrice; }
		if( key > 0 ){
			param.brand = key;
		}
		this.brandKey = key;
		let navigationExtras: NavigationExtras = { queryParams: param };
		this.router.navigate(['/'+this.currentPath], navigationExtras);
	}
	//Price filter
	changePrice(number){
		this.selectedPrice = number;
		let a:number = this.filterContainer.nativeElement.offsetTop;
		window.scrollTo(0, a+80);
		let param:any = {};
		if(this.gender != ''){ param.gender = this.gender; }
		if(number > 0){ param.price = this.selectedPrice; }
		let navigationExtras: NavigationExtras = { queryParams: param };
		this.router.navigate(['/store-offer/', this.currentPath], navigationExtras);
	}
	
	notifyMePopup(itemId){
		this.dataService.sendNotifyme({userId: this.userId, productId: itemId});
	}
	
	scrollToTop(){
		let a:number = this.filterContainer.nativeElement.offsetTop;
		window.scrollTo(0, a-80);
	}
	toTitleCase(str) {
		return str.replace(/\w\S*/g, function(txt){
			return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
		});
	}
}
