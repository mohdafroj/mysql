import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { CustomerService } from '../../_services/pb/customer.service';
import { HttpErrorResponse } from '@angular/common/http';

@Component({
  selector: 'app-failure',
  templateUrl: './failure.component.html',
  styleUrls: ['./../../../assets/css/checkout.css','./failure.component.css']
})
export class FailureComponent implements OnInit {
	orderNumber:any='';
	constructor(private router:Router, private auth: CustomerService) { }

	ngOnInit() {
	  	let successData:string = localStorage.getItem('successData');
		if( successData != null ){
			let successDataObj:any = JSON.parse(successData); 
			this.orderNumber = successDataObj.orderNumber;
			//this.orderMessage = successDataObj.orderMessage;
			localStorage.removeItem('successData');
		}

	}
	
	reOrders(){
		let formData:any = {
			orderNumber:this.orderNumber
		};
		this.auth.reOrder(formData).subscribe(
			res => {
				if(res.status){
					this.router.navigate(['/checkout/cart'], { queryParams: {} });
				}else{
					alert(res.message);
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}

}
