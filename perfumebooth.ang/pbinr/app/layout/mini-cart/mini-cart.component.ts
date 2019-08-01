import { Component, OnInit, Output, EventEmitter,ElementRef, HostListener  } from '@angular/core';
import { DOCUMENT } from '@angular/common';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { ToastrService } 										from 'ngx-toastr';

@Component({
  selector: 'app-mini-cart',
  templateUrl: './mini-cart.component.html',
  styleUrls: ['./mini-cart.component.css']
})
export class MiniCartComponent implements OnInit {

	@Output() passDataFromMiniCartToHeader = new EventEmitter();

	userId:number		= 0;
	total:number		= 0;
	cart:any			= [];
	
	topScrollClass:string 		= 'affix-top';

	constructor(private toastr:ToastrService, private router:Router,private auth:CustomerService,private store:StoreService,private el:ElementRef) {
	
	}

	ngOnInit(){
		this.userId = this.auth.getId();
		this.getMiniCart();
    }

	getMiniCart(){
		this.userId = this.auth.getId();
		this.cart = this.auth.getCart();
		let total = 0;
		let counter = 0;
		for(let item of this.cart){
			total += item.price;
			counter++;
		}
		this.total = total;
		if( counter == this.cart.length ){
			setTimeout(()=>{ this.getMiniCart(); }, 3000);
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

	removeItemFromMiniCartPopup(cartId){
		this.passDataFromMiniCartToHeader.emit({'id':cartId,'message':'Are you sure, you want to delete this product?'});
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
