import { Component, OnInit, Output, EventEmitter, ElementRef, HostListener, ViewEncapsulation  } from '@angular/core';
import { DomSanitizer } 			from '@angular/platform-browser';
import { DOCUMENT } from '@angular/common';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { DataService } from './../../_services/data.service';
import { ToastrService } 	from 'ngx-toastr';

@Component({
  selector: '.mini-cart',
  templateUrl: './mini-cart.component.html',
  styleUrls: ['./mini-cart.component.css'],
  encapsulation: ViewEncapsulation.None
})
export class MiniCartComponent implements OnInit {
	userId = 0;
	total = 0;
	cartLength = 0;
	curencyLogo:string  = '$';
	topScrollClass:string 		= 'affix-top';
	sanitizer:any;
	constructor(
		private router: Router,
		private toastr: ToastrService,
		private customer: CustomerService,
		private store: StoreService,
		private el: ElementRef,
		private dataService: DataService,
		private sanitize: DomSanitizer) {
		this.sanitizer = sanitize;
	}

	ngOnInit(){
		this.userId = this.customer.getId();
		this.getMiniCart();
    }

	getMiniCart(){
		let cart = this.customer.getFromCart(); //console.log(cart['shopping']);
		cart['shopping'] = cart['shopping'] ? cart['shopping'] : {};
		let cartShopping = cart['shopping']['cart'] ? cart['shopping']['cart'].length : 0;
		let packShopping = cart['shopping']['pack'] ? cart['shopping']['pack'].length : 0;
		this.cartLength = cartShopping + packShopping;
		setTimeout( () => { this.getMiniCart(); }, 2000);
	}

	customerLogout(){
		let code = '$2y$10$2kH8FyNLmmt3ZRQ7N6q1fOMZw'+this.userId+'.OnpBadxmZ79oGwl.cyDm0f1Nijm';
		this.userId = 0;
		localStorage.clear();
		this.router.navigate(['/customer/login'],{queryParams:{customerstatus:1,code:code,message:'welcome dear'}});
	}

	viewCart(){
		if ( this.cartLength == 0 ) {
			this.toastr.error('Hi, cart is empty!');
		}
		this.router.navigate(['/checkout/cart/']);
	}

	removeItem(cartId){
		this.dataService.sendDeleteItem({cartId: cartId, userId: this.userId});
	}
	
	@HostListener('window:scroll') onResize() {
		const componentPosition = this.el.nativeElement.offsetTop
		const scrollPosition = window.pageYOffset
		if( scrollPosition > 50 ){
			this.topScrollClass = 'affix';
		}else{
			this.topScrollClass = 'affix-top';
		}
		//console.log(scrollPosition);
	}

}
