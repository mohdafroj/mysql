import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { TrackingService } from './../../_services/tracking.service';

@Component({
  selector: 'app-placed',
  templateUrl: './placed.component.html',
  styleUrls: ['./../../../assets/css/checkout.css','./placed.component.css']
})
export class PlacedComponent implements OnInit {
	orderStep:string = 'process';
	orderNumber:number = 1;
	orderMessage:string = '';
	orderTrack:number = 0;
	paymentGatewayUrl:string = ''
	userId:number = 0;
	customerAuth:string = '';
	
	constructor(private router: Router, private route: ActivatedRoute, private track:TrackingService, private customer: CustomerService, private store: StoreService) {
	}
	ngOnInit() {
		///localStorage.setItem('usdSuccessData', JSON.stringify({'orderNumber':100116759, 'trackFlag':0}));
		//https://www.perfumebooth.com/pb/us-api-v1.0/stores/payment-request?order-id=100127618&customer-id=16597&token=cede1d5bd62f4f10c072178da139906b
		this.userId = this.customer.getId();
		this.customerAuth = this.customer.getToken();
		let usdSuccessData:string = localStorage.getItem('usdSuccessData');
		if( usdSuccessData != null ){
			let successDataObj: any = JSON.parse(usdSuccessData); 
			this.orderNumber = successDataObj.orderNumber;
			this.orderTrack  = successDataObj.trackFlag;
			localStorage.setItem('usdSuccessData', JSON.stringify({'orderNumber': this.orderNumber, 'trackFlag':0}));
		}		
		let formData = {orderNumber: this.orderNumber};
		this.store.getOrderStatus(formData).subscribe(
			res =>{
				switch(res.status){
					case 1: 
						this.orderStep = 'accepted';
						this.customer.setCart([]); //update cart data in logged status
						if( this.orderTrack ) { this.track.trackPurchase(); }
						break;
					case 2: 
						this.orderStep = 'paymentfail';
						break;
					default:
						this.orderStep = 'paymentfail';
				}
				this.orderMessage = res.message;
				this.paymentGatewayUrl  = res.data.redirectUrl;
			},(err: HttpErrorResponse) => {
				//this.router.navigate(['/checkout/unauthorized']);
			}
		);
	}
	
	orderTryAgain(){
		this.router.navigate(['/checkout/cart'], { queryParams: {} });
	}
}
