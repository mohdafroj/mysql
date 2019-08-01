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
	'./../../../assets/css/user-dashboard.css',
	'./wallet.component.css'
	]
})
export class WalletComponent implements OnInit {
	pbPoints:number	     = 0;
	dataList:any;
	constructor(private elem:ElementRef,private titleService: Title, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
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
		let userId:number = this.auth.getId();
		prms = prms.set('userId', `${userId}`);
		this.auth.getWalletDetails(prms).subscribe(
			res => {
				if(res.status){
					this.pbPoints		=	res.data.points;
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	
	getWalletTransactions(){
		let prms = new HttpParams();
		let userId:number = this.auth.getId();
		prms = prms.set('userId', `${userId}`);
		this.auth.getWalletTransactions(prms).subscribe(
			res => {
				this.dataList = res.data;
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	
	

  
}
