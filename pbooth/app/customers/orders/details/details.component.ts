import { Component, ElementRef, ViewChild, OnInit } from '@angular/core';
import { Location } from '@angular/common';
import { Title } from '@angular/platform-browser';
import { Router,ActivatedRoute,Params } from '@angular/router';
import { HttpParams,HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../../_services/pb/myconfig';
import { CustomerService } from '../../../_services/pb/customer.service';
import { SeoService } 	   	from './../../../_services/seo.service';

//declare let jsPDF;
//import * as jsPDF from 'jspdf'
@Component({
  selector: 'app-details',
  templateUrl: './details.component.html',
  styleUrls: [
		'./../../../../assets/css/user-dashboard.css',
		'./details.component.css'
	]
})
export class DetailsComponent implements OnInit {
	@ViewChild('getInvoicePdf', {static: false}) el: ElementRef;
	orderDetails:any=[];
	qParams:any;
	constructor(private seo: SeoService, private loc:Location, private titleService: Title,private router:Router, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
		route.data.subscribe(res =>{
			titleService.setTitle(res.title);
		});
        route.queryParams.subscribe((params: Params) => {
            this.qParams = params;
        });
	}

	ngOnInit() {
		let orderNumber:number = this.qParams['order-number'];
		if(orderNumber > 0){
			this.getOrderDetails(orderNumber);
		}else{
			this.router.navigate(['/customer/orders'], { queryParams: {} });
		}
		this.seo.ogMetaTag('Order Detail Page', 'Order Detail page description');
	}

	getOrderDetails(orderNumber){
		let formData:any = {
			orderNumber:orderNumber
		};
		this.auth.getOrderDetails(formData).subscribe(
			res => {
				if(res.status){
					this.orderDetails = res.data;
				}else{
					this.router.navigate(['/customer/orders'], { queryParams: {} });
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}

	reOrders(orderNumber){
		let formData:any = {
			orderNumber:orderNumber
		};
		this.auth.reOrder(formData).subscribe(
			res => {
				if(res.status){
					this.router.navigate(['/store/cart'], { queryParams: {} });
				}else{
					alert(res.message);
				}
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
			}
		);
	}
	
	getInvoice(orderNumber){
		//let pdf = new jsPDF();
        /*
		let options = {
            pagesplit: true
        };
        pdf.addHTML(this.el.nativeElement, 0, 0, options, () => {
            pdf.save("Invoice-"+orderNumber+".pdf");
        });
		*/
		
		//pdf.text('some text here',10,10);
		//pdf.save('test.pdf');
	}
	
	goBack(){
		this.loc.back();
	}
	
}
