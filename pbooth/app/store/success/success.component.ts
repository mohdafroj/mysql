import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { TrackingService } from './../../_services/tracking.service';
import { CustomerService } from './../../_services/pb/customer.service';
import { StoreService } from './../../_services/pb/store.service';
import { SeoService } from './../../_services/seo.service';


@Component({
  selector: 'app-success',
  templateUrl: './success.component.html',
  styleUrls: ['./../../../assets/css/checkout.css', './success.component.css']
})
export class SuccessComponent implements OnInit {
	orderStep = 'process';
	orderNumber = '';
	orderMessage = '';
	orderTrack = 0;
	paymentGatewayUrl = ''
	
    constructor (
        private seo: SeoService,
        private router: Router,
        private track: TrackingService,
        private customer: CustomerService,
        private store: StoreService
    ) {
    }
    ngOnInit () {
        this.seo.ogMetaTag('Success Page', 'Success page description');
		//localStorage.setItem('successData', JSON.stringify({'orderNumber':100116759, 'trackFlag':0}));
        const successData: string = localStorage.getItem('successData');
        if ( successData != null ) {
            const successDataObj: any = JSON.parse(successData);
            this.orderNumber = successDataObj.orderNumber;
			this.orderTrack  = successDataObj.trackFlag;
        }
		let formData = {orderNumber: this.orderNumber};
		this.store.getOrderStatus(formData).subscribe(
			res =>{
				this.orderMessage = res.message;
				this.paymentGatewayUrl  = res.redirectUrl;
				this.orderStep = res.status;
				if( this.orderStep == 'accepted' ){
					this.customer.setCart([]); //update cart data in logged status
					if( this.orderTrack && window.location.origin.includes('perfumebooth.com') ) { 
						localStorage.setItem('successData', JSON.stringify({'orderNumber': this.orderNumber, 'trackFlag':0}));
						this.track.trackPurchase();
					}
				}
			},(err: HttpErrorResponse) => {
				this.router.navigate(['/checkout/unauthorized']);
			}
		);		
    }

	orderTryAgain(){
		this.router.navigate(['/checkout/cart'], { queryParams: {} });
	}
	
}
