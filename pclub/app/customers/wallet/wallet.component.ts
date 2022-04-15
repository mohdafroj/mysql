import { Component, OnInit, ElementRef } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpParams,HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';

@Component({
  selector: 'app-wallet',
  templateUrl: './wallet.component.html',
  styleUrls: [
	'./../../../assets/css/responsive-table.css',
	'./../../../assets/css/bootstrap-select.css',
	'./wallet.component.css'
	]
})
export class WalletComponent implements OnInit {
	walletTotal = 0;
	cash     = 0;
	dataList;
	constructor ( 
		private elem:ElementRef,
		private titleService: Title, 
		private router: Router, 
		private route: ActivatedRoute, 
		private config:Myconfig, 
		private customer: CustomerService
	) {
		route.data.subscribe(res =>{
			titleService.setTitle(res.title);
		});
	}

	ngOnInit() {
		this.getWalletDetails();
		this.getWalletTransactions();
	}

	getWalletDetails(){
		let prms = new HttpParams();
		let userId:number = this.customer.getId();
		prms = prms.set('userId', `${userId}`);
		this.customer.getWalletDetails(prms).subscribe(
			res => {
				if(res.status){
					this.cash		=	res.data.cash;
					this.walletTotal = res.data.walletTotal;
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	
	getWalletTransactions(){
		let prms = new HttpParams();
		let userId:number = this.customer.getId();
		prms = prms.set('userId', `${userId}`);
		this.customer.getWalletTransactions(prms).subscribe(
			res => {
				this.dataList = res.data;
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	
	

  
}
