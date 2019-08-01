import { Component, OnInit, ViewChild, ElementRef, HostListener } from '@angular/core';
import { Title, Meta, MetaDefinition,DomSanitizer } from '@angular/platform-browser';
import { NgStyle } 	from '@angular/common';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Router, ActivatedRoute, NavigationEnd, Params, NavigationExtras } from '@angular/router';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../../_services/pb/myconfig';
import { ProductsService } from './../../../_services/pb/products.service';
import { CustomerService } from '../../../_services/pb/customer.service';
import { StoreService } from './../../../_services/pb/store.service';
import { TrackingService } from './../../../_services/tracking.service';
import { SeoService } from './../../../_services/seo.service';
import { DataService } from './../../../_services/data.service';
import { ToastrService } from 'ngx-toastr';

@Component({
    selector: 'app-brand-perfume',
    templateUrl: './perfume.component.html',
    styleUrls: [
		'./perfume.component.css'
	]
})

export class BrandPerfumesComponent implements OnInit {
	
    userId:number			= 0;
    currentPath:string		= '';
    brandKey:string         = '';
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
	tagLabelImage           = '';
	comboSection:number		= 0;
	winWidth:number         = 0;
	topScrollClass:string 	= 'affix-top';
	@ViewChild('mainScreen') elementView: ElementRef;
	
	@HostListener('window:load') onLoad() {
		this.winWidth = window.innerWidth;
	}

	@HostListener('window:resize') onResize() {
		this.winWidth = window.innerWidth;
	}

	@HostListener('window:scroll') checkScroll() {
		const scrollPosition:number = window.pageYOffset;
		if( scrollPosition > 300 ){
			this.topScrollClass = 'affix';
		}else{
			this.topScrollClass = 'affix-top';
		}
		const componentPosition:number = this.elem.nativeElement.offsetTop;
		const componentHeight:number = this.elementView.nativeElement.offsetHeight;
		if( this.stopPageLoad && this.resultStatus && (this.productList.length > 0) && (scrollPosition > (componentHeight - componentPosition - 900) ) ){
			this.page += 1;
			this.getMoreProducts();
			//console.log(componentHeight);
		}
		//console.log(scrollPosition);
		//console.log((componentHeight - componentPosition - 900)+" : "+scrollPosition);
	}

	constructor(
		private toastr: ToastrService,
		private meta: Meta,
		private title: Title,
		private router: Router,
		private route: ActivatedRoute,
		private products: ProductsService,
		private auth: CustomerService,
		private store: StoreService,
		private config: Myconfig,
		private elem: ElementRef,
		private track: TrackingService,
		private dataService: DataService,
		private seo: SeoService
		) {
		this.winWidth = window.innerWidth;
		this.userId = this.auth.getId();
		let urls = route.snapshot.url;
		this.currentPath 	= urls[0].path;
		this.brandKey 		= urls[1].path
		this.currentTitle 	= this.toTitleCase(this.currentPath.replace('-', ' '));			
		this.route.queryParams.subscribe((params: Params) => {
			this.queryParams = params;
			if( config.isEmpty(this.queryParams) ){
				route.url.subscribe( res => {
					this.selectedPrice  = 0;
					this.currentPath 	= res[0].path;
					this.brandKey 		= res[1].path;
					this.currentTitle 	= this.toTitleCase(this.currentPath.replace('-', ' '));			
					this.getFilterProducts();
				});
			}else{	
				switch(params.gender){
					case 'male': this.gender = params.gender; break;
					case 'female': this.gender = params.gender; break; 
					default: this.gender = '';
				}
				if( params.price ){ this.selectedPrice = params.price; }
				this.getFilterProducts();
			}
		});

		let ogTitle = ''; let ogDescription = ''; let ogImage = '';
		if ( this.currentPath === 'perfume-bottle' ) { // for perfume bottle
			ogTitle = 'Online Perfume Store : Perfume Bottle For Men and Women';
			ogDescription = 'Buy online original international brands perfumes big bottle at discount price in India. Buy and try Luxury perfumes today!';
			ogImage = 'https://www.perfumebooth.com/assets/images/home/perfume.png';
		} else if ( this.currentPath === 'deodorant' ) { // for deodorant
			ogTitle = 'Deodorants : Buy Deodorants Online for Men and Women at best price';
			ogDescription = 'Perfumebooth Offers you to choose wide range of international brands deodorant and get discounted price.';
			ogImage = 'https://www.perfumebooth.com/assets/images/home/deo.png';			
		} else if ( this.currentPath === 'body-mist' ) {
			ogTitle = 'Body Mist:: Buy Online Body Mist Spray for Women and Men';
			ogDescription = 'Buy online original international brands body mist for women at deals price in India. Perfumebooth offer doorstep perfume delivery.';
			ogImage = 'https://www.perfumebooth.com/assets/images/home/bodymist.png';
		}
		this.seo.ogMetaTag(ogTitle, ogDescription, ogImage);
		
	}
	
	ngOnInit(){
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
		let siteTitle:string = 'Buy Online Perfume Fragrance | Perfume For Men and Women';
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		prms = prms.append('currentPath', `${this.currentPath}`);
		prms = prms.append('brandKey', `${this.brandKey}`);
		prms = prms.append('combo', `${this.comboStatus}`);
		prms = prms.append('price', `${this.selectedPrice}`);
		for (let key in this.queryParams){
		  prms = prms.append(key, this.queryParams[key]);
		}
		
		this.products.getFilterProducts(prms).subscribe(
            res => {
                if(res.status){
					this.productList 			= res.data.products;
					this.totalProduct			= res.data.total;
					this.brands 				= res.data.brands;
					for(let item in this.brands){
						if(this.brands[item].url_key == this.brandKey){ 
							this.brandTitle = this.brands[item].title;
						}
					}
					
					this.comboSection = 0;
					for(let item in this.productList){
						if(this.productList[item].isCombo == "1"){ 
							this.comboSection = 1;
						}
					}
					if( 'all' == this.brandKey){ 
						this.brandTitle = 'All';
					}
				}
				if( this.productList.length < 12 ){
					this.stopPageLoad = 0;
				}
				if( this.productList.length == 0 ){
					//this.router.navigate(['/'+this.currentPath+'/'+this.brandKey]);
				}
				this.resultMsg = res.message;
				this.title.setTitle(siteTitle);
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
	
    getMoreProducts(){
		this.resultStatus = 0;
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		prms = prms.append('currentPath', `${this.currentPath}`);
		prms = prms.append('brandKey', `${this.brandKey}`);
		prms = prms.append('combo', `${this.comboStatus}`);
		prms = prms.append('gender', `${this.gender}`);
		prms = prms.append('page', `${this.page}`);
		prms = prms.append('price', `${this.selectedPrice}`);
		
		for (let key in this.queryParams){
		  prms = prms.append(key, this.queryParams[key]);
		}
		
		this.products.getMoreProducts(prms).subscribe(
            res => {
				this.stopPageLoad = 0;
				for(let item in res){
					if(res[item].isCombo == "1"){ 
						this.comboSection = 1;
					}
					this.stopPageLoad = 1;
					this.productList.push(res[item]);
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
		  this.store.addToCart(formData).subscribe(
			res => {
				if( res.status ){
					this.auth.setCart(res.data.cart);
					for(let i of this.productList){
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
		} else {
		  this.router.navigate(['/customer/registration']);
		}
		
	}
  
	addToWishlist(itemId){
		if( this.userId > 0 ){
			let formData:any = {itemId:itemId};
			this.auth.addToWishlist(formData).subscribe(
				res => {
					alert(res.message);
				},
				(err: HttpErrorResponse) => {
					alert("Sorry, there are some app issue!");
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
			this.router.navigate(['/'+this.currentPath+'/'+this.brandKey], navigationExtras);
		}
	}
	
	comboSelection(va){
		if( va != this.comboStatus ){
			this.comboStatus = va;
			this.getFilterProducts(); 
		}
	}
	
	goToCart(){ this.router.navigate(['/checkout/cart'], {}); }
	
	changeBrand(key){
		this.gender	= '';
		this.comboStatus 	= 0;
		this.router.navigate(['/'+this.currentPath+'/'+key]);
	}
	
	changePrice(number){
		this.config.scrollToTop();
		let param:any = {};
		if(this.gender != ''){
			param.gender = this.gender;
		}
		this.selectedPrice = number;
		if(number > 0){
			param.price = this.selectedPrice;
		}
		let navigationExtras: NavigationExtras = { queryParams: param };
		this.router.navigate(['/'+this.currentPath+'/'+this.brandKey], navigationExtras);
	}
	
	notifyMePopup(itemId){
		this.dataService.sendNotifyme({userId: this.userId, productId: itemId});
	}
	
	toTitleCase(str) {
		return str.replace(/\w\S*/g, function(txt){
			return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
		});
	}
}
