import { Component, OnInit, Output, EventEmitter,ElementRef, HostListener  } from '@angular/core';
import { DomSanitizer } 			from '@angular/platform-browser';
import { DOCUMENT } from '@angular/common';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { DataService } from './../../_services/data.service';
import { ToastrService } 										from 'ngx-toastr';

@Component({
  selector: 'app-mini-cart',
  templateUrl: './mini-cart.component.html',
  styleUrls: ['./mini-cart.component.css']
})
export class MiniCartComponent implements OnInit {
	userId = 0;
	total = 0;
	cart = [];
	curencyLogo:string  = '$';
	topScrollClass:string 		= 'affix-top';
	sanitizer:any;
	constructor(
		private router:Router,
		private customerService:CustomerService,
		private store:StoreService,
		private el:ElementRef,
		private dataService: DataService,
		private sanitize:DomSanitizer) {
		this.sanitizer = sanitize;
	}

	ngOnInit(){
		this.userId = this.customerService.getId();
		this.getMiniCart();
    }

	getMiniCart(){
		this.userId = this.customerService.getId();
		this.cart = this.customerService.getCart(); //console.log(this.cart);
		let total = 0;
		let counter = 0;
		for(let item of this.cart){
			this.curencyLogo  = item.price_logo;
			total += item.price;
			counter++;
		}
		this.total = total;
		if( counter == this.cart.length ){
			setTimeout( () => { this.getMiniCart(); }, 2000);
		}
	}

	customerLogout(){
		let code = '$2y$10$2kH8FyNLmmt3ZRQ7N6q1fOMZw'+this.userId+'.OnpBadxmZ79oGwl.cyDm0f1Nijm';
		this.userId = 0;
		localStorage.clear();
		this.router.navigate(['/customer/login'],{queryParams:{customerstatus:1,code:code,message:'welcome dear'}});
	}

	viewCart(){
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
