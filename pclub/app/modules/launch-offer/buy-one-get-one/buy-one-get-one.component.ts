import { Component, OnInit, HostListener, ElementRef, ViewEncapsulation, ViewChild } from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } from '@angular/platform-browser';
import { Router, ActivatedRoute, NavigationEnd } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { HttpErrorResponse, HttpParams } from '@angular/common/http';
import { Myconfig } from './../../../_services/pb/myconfig';
import { TrackingService } from './../../../_services/tracking.service';
import { ProductsService } from './../../../_services/pb/products.service';
import { CustomerService } from './../../../_services/pb/customer.service';
import { StoreService } from './../../../_services/pb/store.service';

@Component({
  selector: 'buy-one-get-one',
  templateUrl: './buy-one-get-one.component.html',
  styleUrls: ['./buy-one-get-one.component.css']
})
export class BuyOneGetOneComponent implements OnInit {
	@ViewChild('deleteAddListModal', {static: false}) deleteAddListModal: ElementRef;
	@ViewChild('deleteAddProductModal', {static: false}) deleteAddProductModal: ElementRef;
	sanitizer;
	hours = '00';
	minutes = '00';
	seconds = '00';
	dealStatus = 0;
	pack1Price = 0;
	productsPack1 = [];
	productsPack2 = [];
	productsPack3 = [];
	filterProducts = [];
	gender = 'male';
	productType = 'single';
	productDetail = {};
	resultMsg = '';
	resultStatus = 0;
	userId = 0;
	customerCart = [];
	sliderIndex = 0;
	selectedTab = 'A';
	sliderConfig = {};
	getOfferItem = {product_id:0,product_image:'',offer_id:0,offer_image:''};
	constructor (
		private config: Myconfig,
		private track: TrackingService,
		private elem: ElementRef,
		private toastr: ToastrService, 
		private meta: Meta, 
		private title: Title, 
		private sanitize: DomSanitizer,
		private router: Router,
		private route: ActivatedRoute,
		private customer: CustomerService,
		private store: StoreService,
		private product: ProductsService
	) {
		this.sanitizer = sanitize;
	}

	ngOnInit() {
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
		this.userId = this.customer.getId(); //this.customer.doEmptyCart();
		let myCart = this.customer.getFromCart();
		this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
		this.startDealTimer();
		this.getProductList();
		this.updateOfferInCart();
	}
	
	getProductList () {
		this.resultStatus   = 0;
		let prms = new HttpParams();
		prms = prms.append('offerCoupon', '');
		this.product.getProductBuynow(prms).subscribe(
            res => {
                if(res.status){
					this.pack1Price = res.data['pack1Price'];
					this.productsPack1 = res.data['productsPack1'];
					this.productsPack2 = res.data['productsPack2'];
					this.productsPack3 = res.data['productsPack3'];
					this.genderFilter(this.gender);
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

	genderFilter (gender) {
		this.gender = gender;
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
		this.sliderIndex = 0;
		this.selectedTab = 'A';
		this.productDetail = item;
		this.track.clickProduct(item);
	}
	
	addIntoCart (item) {
		if ( this.userId > 0 ) {
			let prodImage = ( item['images'][0]['large'] ) ? item['images'][0]['large'] : '';
			let formData = {itemId: item.id, quantity: 1};
			this.store.addToCart(formData).subscribe(
				res => {
					if ( res.data.cart ) { this.customer.setCart(res.data.cart); }
					if( res.status ){						
						this.toastr.success(res.message);
						this.track.addToCart(item);
						let getOfferItem = this.config.getByKey("getOfferItem");
						if ( getOfferItem != '' ) {
							if ( getOfferItem.product_id == 0 && ( getOfferItem.product_image == '' ) ) {
								this.getOfferItem.product_id = item.id;
								this.getOfferItem.product_image = prodImage;
							} else if ( getOfferItem.offer_id == 0 && ( getOfferItem.offer_image == '' ) ) {
								this.getOfferItem.offer_id = item.id;
								this.getOfferItem.offer_image = prodImage;
							}
						} else {
							this.getOfferItem.product_id = item.id;
							this.getOfferItem.product_image = prodImage;
						}
						this.config.setByKey("getOfferItem", this.getOfferItem);
						this.deleteAddListModal.nativeElement.click();
						this.deleteAddProductModal.nativeElement.click();
						this.updateOfferInCart();
					} else {
						this.toastr.error(res.message);
					}
					let myCart = this.customer.getFromCart();
					this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
				},
				(err: HttpErrorResponse) => {
					this.toastr.error("Sorry, there are some app issue!");
				}
			);
		} else {
			localStorage.setItem('productId', item.id);
			this.router.navigate(['/registration']);
		}
	}
	
	removeItemFromCart (productId) {
		if ( productId >  0 ) {
			this.store.removeCart(productId).subscribe(
				res => {
					if ( res.data.cart ) { this.customer.setCart(res.data.cart); }
					if( res.status ){
						let myCart = this.customer.getFromCart();
						this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
						this.toastr.success(res.message);
						//this.track.removeFromCart(this.removeItem);
					}
				},
				(err: HttpErrorResponse) => {
					this.toastr.warning("Sorry, there are some app issue!");
				}
			);
		}
	}

	removeOfferProduct () {
		let productId = this.getOfferItem.product_id;
		let offerId = this.getOfferItem.offer_id;
		if ( offerId > 0 ) {
			//this.removeItemFromCart(offerId);
		}
		if ( productId > 0 ) {
			//this.removeItemFromCart(productId);
		}
		this.getOfferItem = {product_id:0,product_image:'',offer_id:0,offer_image:''};
		this.config.setByKey("getOfferItem", this.getOfferItem);
		this.updateOfferInCart();
	}
	
	startDealTimer () {
		let deal = this.config.getDeal();
		this.dealStatus = deal.active;
		if ( deal.active == 1 ) {
			this.hours = deal.hours;
			this.minutes = deal.minutes;
			this.seconds = deal.seconds;
		}
		setTimeout( () => { this.startDealTimer(); }, 1000);
	}

	updateOfferInCart () {
		let getOfferItem = this.config.getByKey("getOfferItem");
		if ( getOfferItem != '' ) {
			this.getOfferItem = getOfferItem;
			let inputData = this.store.getCartInfo();
			inputData['getOfferItem'] = this.getOfferItem;
			this.store.setCartInfo(inputData);
		}
	}
}
