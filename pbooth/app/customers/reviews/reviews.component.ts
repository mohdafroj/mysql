import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from './../../_services/pb/customer.service';
import { SeoService } 	   							from './../../_services/seo.service';

@Component({
	selector: 'app-reviews',
	templateUrl: './reviews.component.html',
	styleUrls: [
		'./../../../assets/css/user-dashboard.css',
		'./reviews.component.css'
	]
})
export class ReviewsComponent implements OnInit {
	msg:string;
	reviews:any				= [];
	moreReviewsFlag:number 	= 1;
	loader:number 			= 1;
	page:number				= 1
	constructor(private seo: SeoService, private titleService: Title, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
		route.data.subscribe(res =>{
			titleService.setTitle(res.title);
		});
	}

	ngOnInit() {
		this.seo.ogMetaTag('Customer Review Page', 'Customer Review page description');
		this.getReviews();

	}

	getReviews() {		
		this.auth.getCustomerReviews(this.page).subscribe(
			res => {
				if(res.data['reviews']){
					for( let i of res.data.reviews ){
						this.reviews.push(i);
					}
					
				}
				//console.log(this.reviews);
				this.moreReviewsFlag = res.data['viewMore'] ? res.data.viewMore:0;
				this.loader 	= 0;
			},
			(err: HttpErrorResponse) => {
				console.log("Server Isse!");
				this.loader 	= 0;
			}
		);

	}
	
	loadMoreReviews(){
		this.page = this.page + 1;
		this.getReviews();
	}
	
	getFinalRating(num){
		return this.config.numToArray(num); //Array.from(Array(num).keys());
	}
	
	getRemainingRating(num){
		let a = 5 - num;
		return this.config.numToArray(a); //Array.from(Array(a).keys());
	}
}
