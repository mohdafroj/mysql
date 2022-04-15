import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router,ActivatedRoute } from '@angular/router';
import { HttpParams,HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../../_services/pb/myconfig';
import { CustomerService } from '../../../_services/pb/customer.service';

@Component({
  selector: 'pc-order-list',
  templateUrl: './list.component.html',
  styleUrls: [
		'./list.component.css'
	],
  encapsulation: ViewEncapsulation.None
})
export class ListComponent implements OnInit {
	ordersList = [];
	orderId = '';
	userId = 0;
	confimMsg ='';
	serverRequest = true;
	loader = 1;
	constructor(
		private router: Router, 
		private route: ActivatedRoute, 
		private config: Myconfig, 
		private customer: CustomerService) {
	}

	ngOnInit() {
		this.config.scrollToTop();
		this.userId = this.customer.getId();
		this.getOrdersList('');
	}
  
	getOrdersList(orderBy){
		this.ordersList = [];
		let prms = new HttpParams();
		prms = prms.set('userId', `${this.userId}`);
		prms = prms.set('orderBy', `${orderBy}`);
		if ( this.serverRequest ) {
			this.serverRequest = false;
			this.customer.getOrders(prms).subscribe(
				res => {
					this.ordersList = res.data;
					this.loader = 0;
					this.serverRequest = true;
				},
				(err: HttpErrorResponse) => {
					console.log("Server Isse!");
					this.loader = 0;
					this.serverRequest = true;
				}
			);
		}
	}
	
	getOrderDetails(orderId){
		let formData = {
			orderId: orderId
		};
		if ( this.serverRequest ) {
			this.serverRequest = false;		
			this.customer.getOrderDetails(formData).subscribe(
				res => {
					if(res.status){
						
					}else{
						alert(res.message);
					}
					this.serverRequest = true;
				},
				(err: HttpErrorResponse) => {
					console.log("Server Isse!");
					this.serverRequest = true;
				}
			);
		}	
	}
  
	reOrders(orderId){
		let formData = {
			orderNumber: orderId
		};
		if ( this.serverRequest ) {
			this.serverRequest = false;
			this.customer.reOrder(formData).subscribe(
				res => {
					if(res.status){
						this.router.navigate(['/checkout/cart'], { queryParams: {} });
					}else{
						alert(res.message);
					}
					this.serverRequest = true;
				},
				(err: HttpErrorResponse) => {
					console.log("Server Isse!");
					this.serverRequest = true;
				}
			);
		}
	}
  
	orderCancelPopup(orderId){
		this.orderId = orderId;
		this.confimMsg = 'Are you sure, you want to cancel this order!';
		return true;
	}
  
	cancelOrders(){
		this.confimMsg = 'Please wait...!';
		let formData = {
			orderNumber:this.orderId
		};
		if ( this.serverRequest ) {
			this.serverRequest = false;
			this.customer.cancelOrder(formData).subscribe(
				res => {
					this.confimMsg = res.message;
					this.serverRequest = true;
				},
				(err: HttpErrorResponse) => {
					console.log("Server Isse!");
					this.serverRequest = true;
				}
			);
		}
	}
  
}
