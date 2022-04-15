import { Component, OnInit, ElementRef, ViewEncapsulation, ViewChild } from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } from '@angular/platform-browser';
import { ToastrService } from 'ngx-toastr';
import { ReactiveFormsModule, FormsModule, FormControl } from '@angular/forms';
import { Router, ActivatedRoute, NavigationEnd } from '@angular/router';
import { HttpErrorResponse, HttpParams } from '@angular/common/http';
import { CustomerService } from './../../../_services/pb/customer.service';
import { StoreService } from './../../../_services/pb/store.service';
import { ProductsService } from './../../../_services/pb/products.service';
import { TrackingService } from './../../../_services/tracking.service';
import { Myconfig } from './../../../_services/pb/myconfig';

@Component({
  selector: 'scent-match-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class DashboardComponent implements OnInit {
	userId = 0;
	customerCart = [];
	tabIndex = 'A';
	sliderIndex = 0;
	gender = '';
	brandId = 0;
	families = [];
	brands = [];
	algoProducts = [];
	selectedFamily = [];
	favoriteKeywords = [];
	sanitizer;
	productList = [];
	productDetail = {};
	resultStatus = 0;
	resultMsg = '';
	myControl;
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
		private product: ProductsService
	) {
		this.sanitizer = sanitize;
	}

	ngOnInit() {
		this.config.scrollToTop();
		this.userId = this.customer.getId(); 
		let myCart = this.customer.getFromCart();
		this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
		this.getScentMatch();
		this.myControl = new FormControl('');
	}
	
	getScentMatch(){
		let favoritePerfume = this.favoriteKeywords.map( (item) => item.id );
		this.resultStatus   = 0;
		this.productList = [];
		let prms = new HttpParams();
		prms = prms.append('gender', `${this.gender}`);
		prms = prms.append('brand', `${this.brandId}`);
		prms = prms.append('families', `${this.selectedFamily}`);
		prms = prms.append('favoritePerfumes', `${favoritePerfume}`);
		this.product.getScentMatch(prms).subscribe(
            res => {
                if(res.status){
					this.productList = res.data['products'];
					if ( this.families.length == 0 ) {
						let family = res.data['family'];
						for ( let i = 0; i < family.length; i++ ) {
							family[i]['checked'] = '';
						}
						this.families = family;
						this.brands = res.data['brands'];
						this.brandId = ( this.brands[0] ) ? this.brands[0]['id'] : 0;
						this.getAlgoProducts('');
					}
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
	
	getSelectedValue(item) {
		this.brandId = item.target.value;
	}
	
	getAlgoProducts(search) {
		let prms = new HttpParams();
		prms = prms.append('search', search);
		this.product.getAlgoProducts(prms).subscribe(
            res => {
				this.algoProducts = res['data'];
            },
            (err: HttpErrorResponse) => {
            }
        );
	}
	
	selectFamily (item, e) {
		if ( this.selectedFamily.includes(item.id) ) {
			this.selectedFamily = this.selectedFamily.filter( (s) => { return s != item.id; }, item.id );
		} else if ( this.selectedFamily.length > 2 ) {
			this.toastr.error("Sorry, family limit exceeded..");
			e.target.checked = false;
		} else {
			for ( let i = 0; i < this.families.length; i++ ) {
				if ( this.families[i]['id'] == item.id ) {
					if (this.families[i]['checked'] != 'checked') {
						this.families[i]['checked'] = 'checked';
						this.selectedFamily.push(item.id);
					} else {
						this.families[i]['checked'] = '';
						this.selectedFamily = this.selectedFamily.filter( (s) => { return s != item.id; }, item.id );
					}
				}
			}
		}
	}
	
	addKeywordsOld (text1) {
		if ( text1 != '' ) {
			let selectedCase = this.favoriteKeywords.filter( (item) => { return text1 == item.name; }, text1 )[0];
			if ( this.favoriteKeywords.length > 2 ) {
				this.toastr.error("Sorry, you can not select more than 3 keywords!");
			} else {
				if ( selectedCase == undefined ) {
					let keyword = this.algoProducts.filter( (item) => { return text1 == item.name; }, text1 )[0];
					if ( keyword == undefined ) {
						this.toastr.error("Keyword should be from suggestion box!");
					} else {
						this.favoriteKeywords.push(keyword);
						this.myControl.reset('');
					}
				} else {
					this.toastr.error("Sorry, already selected!");
				}
			}
		} else {
			this.toastr.error("Please select a value!");
		}		
	}

	addKeywords (text1) {
		if ( text1 != '' ) {
			let keyword = this.algoProducts.filter( (item) => { return text1 == item.name; }, text1 )[0];
			this.favoriteKeywords = [];
			if ( keyword == undefined ) {
				this.toastr.error("Keyword should be from suggestion box!");
			} else {
				this.favoriteKeywords.push(keyword);
			}
		} else {
			this.toastr.error("Please select a product from drowpdown list!");
		}		
	}

	removeKeywords (item) {
		this.favoriteKeywords = this.favoriteKeywords.filter( (s) => { return s.id != item.id; }, item );
	}
	
	viewProduct(item) {
		this.productDetail = item;
		this.track.clickProduct(item);
	}
	
	addIntoCart( item ) {
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
					let myCart = this.customer.getFromCart();
					this.customerCart = myCart['shopping']['cart'].map( (item) => { return item.id; });
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

}
