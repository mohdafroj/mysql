import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';

@Component({
  selector: 'app-placed',
  templateUrl: './placed.component.html',
  styleUrls: ['./../../../assets/css/checkout.css', './placed.component.css']
})
export class PlacedComponent implements OnInit {
	orderStep = 'process';
	orderNumber = 1;
	orderMessage = '';
	orderTrack = 0;
	paymentGatewayUrl = ''
	userId = 0;
	customerAuth = '';
	
    constructor( 
		private router: Router,
		private route: ActivatedRoute,
		private customer: CustomerService,
		private store: StoreService) 
	{
    }
    ngOnInit () {
        // localStorage.setItem('successData', JSON.stringify({'orderNumber':'123456','orderMessage':'order placed!'}));
        // this.router.navigate(['/store/order-success']);
        this.route.queryParams.subscribe((params: Params) => {
            const formData: any = {
                orderNumber: params['order-number'],
                pgStatus: params['status'],
                pgName: params['pg-name'],
                pgData: params['pg-data']
            };
            this.store.updateOrderDetails(formData).subscribe(
                res => {
                    localStorage.setItem('successData', JSON.stringify({'orderNumber': res.data.orderNumber, 'trackFlag': 1, 'orderMessage': res.message}));
                    /**if ( res.status && (res.data.returnStatus === 'accepted') ) {
                        this.router.navigate(['/checkout/onepage/success']);
                    } else {
                        this.router.navigate(['/checkout/onepage/failure']);
                    }****/
					this.router.navigate(['/checkout/onepage/success']);
                }, (err: HttpErrorResponse) => {
                    this.router.navigate(['/checkout/unauthorized']);
                }
            );
        });
		
		///localStorage.setItem('successData', JSON.stringify({'orderNumber':100116759, 'trackFlag':0}));
		/***
		this.customerAuth = this.customer.getToken();
		this.userId = this.customer.getId();
		let successData:string = localStorage.getItem('successData');
		if( successData != null ){
			let successDataObj: any = JSON.parse(successData); 
			this.orderNumber = successDataObj.orderNumber;
			this.orderTrack  = successDataObj.trackFlag;
			localStorage.setItem('successData', JSON.stringify({'orderNumber':this.orderNumber, 'trackFlag':0}));
		}		
		let formData = {orderNumber: this.orderNumber};
		this.store.getOrderStatus(formData).subscribe(
			res =>{
				switch(res.status){
					case 1: 
						this.orderStep = 'accepted';
						this.customer.setCart([]); //update cart data in logged status
						//if( this.orderTrack ) { this.track.trackPurchase(); }
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
				
			}
		);
		***/
    }
}
