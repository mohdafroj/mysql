import { Component, OnInit, ElementRef } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpParams,HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from './../../_services/pb/customer.service';
import { SeoService } 	   							from './../../_services/seo.service';

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
	totalBallence:number = 0;
	pbCash:number        = 0;
	pbPoints:number	     = 0;
	v5:number			 = 0;
	v3:number			 = 0;
	dataList:any;
	constructor(private seo: SeoService, private elem:ElementRef,private titleService: Title, private router: Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
		route.data.subscribe(res =>{
			titleService.setTitle(res.title);
		});
	}

	ngOnInit() {
		this.seo.ogMetaTag('Customer Wallet Page', 'Customer wallet page description');
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
					this.totalBallence	=	res.data.grand_total;
					this.pbCash			=	res.data.pb_cash_amount;
					this.pbPoints		=	res.data.pb_points_amount;
					this.v5				=	res.data.v5;
					this.v3				=	res.data.v3;
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
