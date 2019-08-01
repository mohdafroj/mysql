import { Component,OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { TrackingService } from './../../_services/tracking.service';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from '../../_services/pb/store.service';


@Component({
  selector: 'app-success',
  templateUrl: './success.component.html',
  styleUrls: ['./../../../assets/css/checkout.css','./success.component.css']
})
export class SuccessComponent implements OnInit{
	orderNumber:any 	= '';
	orderMessage:any 	= '';
	constructor(private router:Router, private track:TrackingService, private customer:CustomerService, private store:StoreService) {
	}
	ngOnInit(){
		let successData:string = localStorage.getItem('successData');
		if( successData != null ){
			this.track.trackPurchase();
			let successDataObj:any = JSON.parse(successData); 
			this.orderNumber = successDataObj.orderNumber;
			this.orderMessage = successDataObj.orderMessage;
			this.customer.setCart([]); //update cart data in logged status
			localStorage.removeItem('successData');
			this.pushOrderToVendors(this.orderNumber);
		}else{
			this.router.navigate(['/store/unauthorized']);
		}
	}
	
	pushOrderToVendors(orderId:any){
		let formData = {orderNumber:orderId};
		this.store.pushOrderToVendors(formData).subscribe(
			res => {
			}, err => {
			}
		);
		return true;
	}
}
