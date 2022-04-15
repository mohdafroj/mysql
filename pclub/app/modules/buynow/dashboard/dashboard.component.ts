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

@Component({
  selector: 'buynow-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
	subHeader = '';
	userId = 0;
	marquee = ''
	userMenu = false;
	basePath = '';
	customerCart = [];
	resultStatus = 0;
	resultMsg = '';
	sanitizer;
	pack1Price = 0;
	pack2Price = 0;
	pack3Price = 0;
	productsPack1 = [];
	productsPack2 = [];
	productsPack3 = [];
	filterProducts = [];
	queryParamData = {};
	productDetail = {};
	gender = 'male';
	productType = 'single';
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
	constructor ( 
		private config: Myconfig,
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
		let packPrices = this.config.packPrices;
		this.pack1Price = packPrices[0];
		this.pack2Price = packPrices[1];
		this.pack3Price = packPrices[2];
		this.sliderConfig = {
		  autoplay: false,
		  infinite: false,
		  dots: false,
		  arrows: false,
		  slidesToShow: 1,
		  slidesToScroll: 1,
		  asNavFor:'.slick-carousel',
		  responsive: [
			{
			  breakpoint: 667,
			  settings: {
				slidesToShow: 1.3,
				slidesToScroll: 1
			  }
			}
		  ]
		};
		this.config.scrollToTop();
		this.userId = this.customer.getId(); //this.customer.doEmptyCart();
		let myCart = this.customer.getFromCart();
		this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
		//console.log(this.customerCart);
		this.route.queryParamMap.subscribe(res => {
			this.gender = (res['params']['gender'] != undefined) ? res['params']['gender'] : 'male';
			this.productType = (res['params']['type'] != undefined) ? res['params']['type'] : 'single';
			this.productKey = (res['params']['product'] != undefined) ? res['params']['product'] : '';
			if ( this.gender != this.oldGender || this.productType != this.oldProductType ) {
				this.oldProductType = this.productType;
				this.oldGender = this.gender;
				this.selectedIndex = 0;
			}
			if ( this.filterProducts.length > 0 ) {
				//this.genderFilter();
			}
			this.genderFilter();
		});  
		let checkUrl = this.router.url.split("?");
		checkUrl = checkUrl[0].split("/");
		this.basePath = checkUrl[1];
		this.getProductList();
		this.getMiniCart();
		let infoData = this.pages.getCompanyData();
		this.stripBanner = ( infoData && (infoData['company'] != undefined) && (infoData['company']['strip'] != '') ) ? infoData['company']['strip'] : '';

	}
	
	slickInit(e) {
		//console.log('slick initialized');
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
		this.product.getProductBuynow(prms).subscribe(
            res => {
                if(res.status){
					this.productsPack1 = res.data['productsPack1'];
					this.productsPack2 = res.data['productsPack2'];
					this.productsPack3 = res.data['productsPack3'];
					this.pack1Price = res.data['pack1Price'];
					this.pack2Price = res.data['pack2Price'];
					this.pack3Price = res.data['pack3Price'];
					this.config.setOfferCoupon(res.data['offerCoupon']);
					this.marquee = res.data['marquee'];
					this.config.setMeta({title: res.data['meta']['title'], keywords: res.data['meta']['keywords'], description: res.data['meta']['description']});
					this.genderFilter();
					this.startOfferTimer();
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

	addIntoCart (item) {
		let myCart = this.customer.getFromCart();
		if ( this.userId > 0 ) {
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
						this.track.addToCart(item);
					} else {
						this.toastr.error(res.message);
					}
					myCart = this.customer.getFromCart();
					this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
					this.getMiniCart();
				},
				(err: HttpErrorResponse) => {
					this.toastr.error("Item not added: Sorry, there are some app issue!");
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
				images: [{id: 0, product_id: item.id, title: (item.images[0]['title'] == undefined ) ? '' : item.images[0]['title'], alt: (item.images[0]['alt'] == undefined) ? '': item.images[0]['alt'], url: (item.images[0]['small'] == undefined) ? '' : item.images[0]['small'] }],
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
			this.getMiniCart();
		}
	}

	removeItemFromCart () {
		this.removeAction = 1;
		let myCart = this.customer.getFromCart();
		let productId = this.removeItem['id'] !=  "undefined" ? this.removeItem['id'] : 0;
		let shoppingCart = myCart['shopping']['cart'].filter( (item) => { return item.id != productId; }, productId);
		if ( this.userId > 0 ) {
			let carItemIds = shoppingCart.map( (item) => { return item.id; });
			let carItemQuantities = shoppingCart.map( (item) => { return item.cart_quantity; });
			carItemIds = carItemIds.join(',');
			carItemQuantities = carItemQuantities.join(',');
			let formData = {itemId: carItemIds, quantity: carItemQuantities, useraction: 'remove'};
			this.store.addToCart(formData).subscribe(
				res => {
					if ( res.data.cart ) { this.customer.setCart(res.data.cart); }
					if( res.status ){								
						this.deleteConfirmModal.nativeElement.click();
						this.removeItem = {};
						this.toastr.success(res.message);
						this.track.removeFromCart(this.removeItem);
					} else {
						this.toastr.error(res.message);
					}
					this.removeAction = 0;
					myCart = this.customer.getFromCart();
					this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
					this.getMiniCart();
				},
				(err: HttpErrorResponse) => {
					this.toastr.error("Item not removed: Sorry, there are some app issue!");
					this.removeAction = 0;
				}
			);						
		} else {
			this.customer.setCart(shoppingCart);
			this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
			this.getMiniCart();
			this.toastr.success("Item removed from cart!");
			this.track.removeFromCart(this.removeItem);
			this.removeItem = {};
			this.deleteConfirmModal.nativeElement.click();
			this.removeAction = 0;
		}
	}
	
	genderFilter () {
		let unisex = [];
		let products = [];
		switch ( this.productType ) {
			case 'pack-3': 
				unisex = this.productsPack3.filter( (item) => { return item.gender == 'unisex'; }, '');
				break;
			case 'pack-2': 
				unisex = this.productsPack2.filter( (item) => { return item.gender == 'unisex'; }, '');
				break;
			default: 
				unisex = this.productsPack1.filter( (item) => { return item.gender == 'unisex'; }, '');
		}
		if ( this.gender == 'unisex' ) {
			products = this.productsPack1.filter( (item) => { return item.gender == 'mfemale'; }, '');
			this.filterProducts = products.concat(unisex);
		} else {
			switch ( this.productType ) {
				case 'pack-3': 
					products = this.productsPack3.filter( (item) => { return item.gender == this.gender; }, this.gender);
					break;
				case 'pack-2':
					products = this.productsPack2.filter( (item) => { return item.gender == this.gender; }, this.gender);
					break;
				default: 
					products = this.productsPack1.filter( (item) => { return item.gender == this.gender; }, this.gender);
			}
			this.filterProducts = products.concat(unisex);
		}
		//console.log(this.gender, this.productType, this.selectedIndex, this.filterProducts);
		return true;
	}

	viewProduct(item) {
		this.productDetail = item;
		this.track.clickProduct(item);
	}

	getMiniCart () {
		this.cartTotal = 0;
		this.userId = this.customer.getId();
		let cart = this.customer.getFromCart(); 
		cart['shopping'] = cart['shopping'] ? cart['shopping'] : {};
		this.shoppingCart = cart['shopping']['cart'] ? cart['shopping']['cart'] : [];
		this.shoppingPack = cart['shopping']['pack'] ? cart['shopping']['pack'] : [];
		this.cartLength = this.shoppingCart.length + this.shoppingPack.length;
		for ( let i = 0; i < this.shoppingCart.length; i++ ) {
			this.cartCurrency = this.shoppingCart[i]['price_logo'];
			this.cartTotal += this.shoppingCart[i]['cart_quantity'] * this.shoppingCart[i]['price'];
		}
		for ( let i = 0; i < this.shoppingPack.length; i++ ) {
			this.cartTotal += this.shoppingPack[i]['cart_quantity'] * this.shoppingPack[i]['price'];
		}
	}

	customerLogout () {
		this.customer.customerLogout();
	}
	
	startOfferTimer () {
		let obj = this.config.getSaleStrip(); 
		if ( Object.keys(obj).length && (this.minutes != '') ) {
			this.stripBanner = '';
			this.days = obj['days'];
			this.hours = obj['hours'];
			this.minutes = obj['minutes'];
			this.seconds = obj['seconds']; //console.log(obj);
		} else {
			//this.config.setOfferCoupon(''); //set offer coupon empty
			let infoData = this.pages.getCompanyData();
			this.stripBanner = ( ( infoData != undefined ) && (infoData['company'] != undefined) && (infoData['company']['strip'] != undefined) && (infoData['company']['strip'] != '') ) ? infoData['company']['strip'] : '';
		}
		setTimeout( () => { this.startOfferTimer(); }, 1000);
	}
	
	@HostListener('window:scroll') checkScroll() {
		const scrollPosition:number = window.pageYOffset;
		if( scrollPosition > 50 ){
			this.subHeader = 'sticky-top-affix';
		}else{
			this.subHeader = '';
		}
	}
	
	

}
