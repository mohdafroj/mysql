import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { Myconfig } from './../../_services/pb/myconfig';

@Component({
  selector: 'pc-placed',
  templateUrl: './placed.component.html',
  styleUrls: ['./placed.component.css']
})
export class PlacedComponent implements OnInit {
	orderStep = 'process';
	orderNumber = 0;
	orderMessage = '';
	paymentGatewayUrl = ''
	userId = 0;
	customerAuth:string = '';
	
	constructor(
		private router: Router, 
		private customer: CustomerService, 
		private store: StoreService,
		private config: Myconfig
	) {
	}
	ngOnInit() {
		this.paymentGatewayUrl = this.config.paymentGatewayUrl;
		this.userId = this.customer.getId();
		this.customerAuth = this.customer.getToken();
		let successData = this.store.getSuccessData();
		if( Object.keys(successData).length ){
			this.orderNumber = successData['orderNumber'];
		}
		this.store.getOrderStatus(successData).subscribe(
			res => {
				this.orderMessage = res.message;
				switch ( res.status ) {
					case 1: 
						this.orderStep = 'accepted';
						this.router.navigate(['/checkout/onepage/success']);
						break;
					case 2: 
						this.orderStep = 'paymentfail';
						break;
					default:
						this.orderStep = 'paymentfail';
				}
			},( err: HttpErrorResponse ) => {
				this.router.navigate(['/checkout/unauthorized']);
			}
		);
	}
	
	orderTryAgain(){
		this.router.navigate(['/checkout/cart'], { queryParams: {} });
	}
}
