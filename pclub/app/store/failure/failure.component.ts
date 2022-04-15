import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { CustomerService } from '../../_services/pb/customer.service';
import { StoreService } from '../../_services/pb/store.service';
import { HttpErrorResponse } from '@angular/common/http';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'pc-failure',
  templateUrl: './failure.component.html',
  styleUrls: ['./failure.component.css']
})
export class FailureComponent implements OnInit {
	orderNumber = '';
	constructor ( 
		private router: Router, 
		private toastr: ToastrService,
		private store: StoreService,
		private customer: CustomerService
	) {
		
	}
	
	ngOnInit() {
	  	let successData = this.store.getSuccessData();
		if( Object.keys(successData).length ){
			this.orderNumber = successData['orderNumber'];
			this.store.setSuccessData({});
		}
	}
	
	reOrders(){
		this.customer.reOrder({orderNumber: this.orderNumber}).subscribe(
			res => {
				if(res.status){
					this.router.navigate(['/checkout/cart'], { queryParams: {} });
				}else{
					this.toastr.error(res.message);
				}
			},
			(err: HttpErrorResponse) => {
				this.toastr.error("Server Isse!");
			}
		);
	}

}
