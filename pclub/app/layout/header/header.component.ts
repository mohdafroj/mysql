import { Component, OnInit, ElementRef, ViewEncapsulation, HostListener } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { Location } from '@angular/common';
import { Router, ActivatedRoute, NavigationEnd } from '@angular/router';
import { CustomerService } from './../../_services/pb/customer.service';
import { ToastrService } from 'ngx-toastr';
import { DataService } from './../../_services/data.service';
import { Myconfig } from './../../_services/pb/myconfig';

@Component({
  selector: 'header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class HeaderComponent implements OnInit {
    windowWidth:number = 0;
	windowHeight:number = 0;
	homeLink = '';
	actveMenu = false;
	showCart = false;
	showCartMenu = true;
	cartCurrency;
	userId = 0;
	cartTotal = 0;
	cartLength = 0;
	shoppingCart = [];
	shoppingPack = [];
	topScrollClass:string = 'affix-top';
	shonowSticky = 0;
	sanitizer:any;	
	constructor(
		private router: Router,
		private route: ActivatedRoute, 
		private toastr: ToastrService,
		public config: Myconfig, 
		private customer: CustomerService,
		private elem: ElementRef,
		private dataService: DataService,
		private sanitize: DomSanitizer) {
		this.sanitizer = sanitize;
	}
	
	ngOnInit(){
		this.windowWidth = window.innerWidth;
		this.windowHeight = window.innerHeight;
		this.cartCurrency = this.config.cartCurrency; 
		this.homeLink = (this.router.url == '/winter-sale-offer') ? this.router.url : '/';
		this.router.events.subscribe(event => {
			if(event instanceof NavigationEnd) {
				let urlAr:any = event.url.split('/');
				this.showCartMenu = ( this.config.footerHiddenPages.indexOf(urlAr[2]) != -1 ) ? false : true;				
			}
		});
		this.getMiniCart();
    }
	
	menuToggle () {
		this.elem.nativeElement.querySelector('#menuToggle').click();
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
		setTimeout( () => { this.getMiniCart(); }, 2000);
		//console.log(this.shoppingCart);
	}

	onLogged (userId: number) {
		this.userId = userId;
	}
	
	customerLogout(){
		this.customer.customerLogout();
	}

	removeItem(cartId){
		this.dataService.sendDeleteItem({cartId: cartId, userId: this.userId});
	}
	
	@HostListener('window:scroll') onResize() {
		const componentPosition = this.elem.nativeElement.offsetTop
		const scrollPosition = window.pageYOffset
		if( scrollPosition > 50 ){
			this.shonowSticky = 1;
		}else{
			this.shonowSticky = 0;
		}
		//console.log(scrollPosition);
	}
	
	@HostListener('window:scroll') checkScroll() {
		const scrollPosition = window.pageYOffset;
		if( scrollPosition > 50 ){
			this.shonowSticky = 1;
		}else{
			this.shonowSticky = 0;
		}
	}

	@HostListener('window:resize', ['$event'])

	resizeWindow() {
		this.windowWidth = window.innerWidth;
		this.windowHeight = window.innerHeight;
	}
	
}
