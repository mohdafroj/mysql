import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router,ActivatedRoute } from '@angular/router';
import { HttpParams,HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../../_services/pb/myconfig';
import { CustomerService } from './../../../_services/pb/customer.service';
import { SeoService } 	   	from './../../../_services/seo.service';

@Component({
  selector: 'app-list',
  templateUrl: './list.component.html',
  styleUrls: [
		'./../../../../assets/css/user-dashboard.css',
		'./list.component.css'
	]
})
export class ListComponent implements OnInit {
	ordersList:any=[];
	ordersClass:string='active';
	cancelClass:string='';
	
	orderId:any 	='';
	confimMsg:string	='';
	
	loader:number 	= 1;
	
	constructor(private seo: SeoService, private router:Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
	}

	ngOnInit() {
		let orderBy:string='';
		this.getOrdersList(orderBy);
		this.seo.ogMetaTag('Order List Page', 'Order List page description');
	}
  
	getOrdersList(orderBy){
		this.ordersList = [];
		let prms = new HttpParams();
		let userId:number = this.auth.getId();
		prms = prms.set('userId', `${userId}`);
		prms = prms.set('orderBy', `${orderBy}`);
		this.auth.getOrders(prms).subscribe(
			res => {
				this.ordersList = res.data;
				this.loader = 0;
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
				this.loader = 0;
			}
		);
	}
	
	selectOrders(orderBy){
		this.loader = 1;
		if(orderBy == 'cancel'){
			this.ordersClass = '';
			this.cancelClass = 'active';
		}else{
			this.ordersClass = 'active';
			this.cancelClass = '';
		}
		this.getOrdersList(orderBy);
	}
  
	getOrderDetails(orderId){
		let formData:any = {
			orderId:orderId
		};
		this.auth.getOrderDetails(formData).subscribe(
			res => {
				if(res.status){
					
				}else{
					alert(res.message);
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
  
	reOrders(orderId){
		let formData:any = {
			orderNumber:orderId
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
  
	orderCancelPopup(orderId){
		this.orderId = orderId;
		this.confimMsg = 'Are you sure, you want to cancel this order!';
		return true;
	}
  
	cancelOrders(){
		this.confimMsg = 'Please wait...!';
		let formData:any = {
			orderNumber:this.orderId
		};
		this.auth.cancelOrder(formData).subscribe(
			res => {
				//alert(res.message);
				this.confimMsg = res.message;
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
  
}
