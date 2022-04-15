import { Component,OnInit } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from '../../_services/pb/store.service';
import { PagesService } from '../../_services/pb/pages.service';
import { TrackingService } from '../../_services/tracking.service';
import { Myconfig } from './../../_services/pb/myconfig';


@Component({
  selector: '[pc-success]',
  templateUrl: './success.component.html',
  styleUrls: ['./success.component.css']
})
export class SuccessComponent implements OnInit{
	orderNumber 	= '';
	orderTotal = 0;
	orderCurrency = '';
	orderPrefix 	= '';
	orderMessage 	= '';
	company;
	sanitizer:any;
	constructor ( 
		private router: Router, 
		private pages: PagesService, 
		private customer: CustomerService,
		private track: TrackingService,
		private store: StoreService,
		private config: Myconfig,
		private sanitize: DomSanitizer
	) {
		this.sanitizer = sanitize
	}
	ngOnInit(){
		let infoData = this.pages.getCompanyData()['company'];
		this.company = (infoData != undefined) ? infoData : {email: "connect@perfumersclub.com"}; 
		let successData = this.store.getSuccessData();
		//let successData = {orderNumber:123456, orderTotal: 1150, orderPrefix: 'PC', orderCurrency: this.config.cartCurrency, trackFlag:1, orderMessage: ''};
		let trackFlag = ( successData['trackFlag'] != undefined ) ? successData['trackFlag'] : 1;
		if( Object.keys(successData).length ){
			this.orderPrefix = (successData['orderPrefix'] != undefined) ? successData['orderPrefix'] : '';
			this.orderNumber = successData['orderNumber'];
			this.orderTotal = (successData['orderTotal'] != "undefined") ? successData['orderTotal'] : 0;
			this.orderCurrency = (successData['orderCurrency'] != "undefined") ? successData['orderCurrency'] : this.config.cartCurrency;
			this.orderMessage = successData['orderMessage'];
			if( trackFlag ) {
				successData['trackFlag'] = 0;
				this.store.setSuccessData(successData);
				this.track.storeTrack('purchase'); 
			}
			this.customer.setCart([]); //update cart data in logged status
		} else {
			this.router.navigate(['/checkout/unauthorized']);
		}
	}	
}
