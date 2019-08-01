import { Component, Input, OnInit, OnDestroy } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { DomSanitizer } from '@angular/platform-browser';
import { Router } from '@angular/router';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { ProductsService } from './../../../_services/pb/products.service';
import { CustomerService } from './../../../_services/pb/customer.service';
import { DataService } from './../../../_services/data.service';
import { Subscription } from 'rxjs';
@Component({
  selector: 'reviews',
  templateUrl: './reviews.component.html',
  styleUrls: ['./reviews.component.css']
})
export class ReviewsComponent implements OnInit, OnDestroy {
  reviewMsg	= '';
  reviewStatus = 0;
  rForm: FormGroup;
	reviewSelected				= [1,2,3,4,5];
	reviewsList: any					= [];
	reviewRemain: any				= [];
	reviewsLoader			        = '';
	moreReviewsFlag: number   		= 1;
	reviewsPage: number				= 1;
	progressRating = [];
	customerReviews = 0;
	customerRating = 0;
    result = {id:0, reviews: [], progressRating: [] };
    @Input() userId = 0;
    sanitizer:any;
	subscription: Subscription;
  
  constructor (
    private router: Router,
	private sanitize: DomSanitizer,
	private products: ProductsService,
	private customer: CustomerService,
	private dataService: DataService
  ) {
       this.sanitizer = sanitize;
  }

  ngOnInit() {
		this.rForm = new FormGroup ({
			title: new FormControl('', Validators.compose([Validators.required]) ),
			description: new FormControl('', Validators.compose([Validators.required]) )
		});
		
		this.subscription = this.dataService.getReviews().subscribe(res => {
          if ( res ) {
            this.result = res; //console.log(res);
          }
		this.reviewsList = (this.result['reviews'] == undefined) ? [] : this.result.reviews;
		this.progressRating = (this.result['progressRating'] == undefined) ? [] : this.result.progressRating;
		if( this.result['custReviews'] != undefined ){
			if ( this.result['custReviews']['customers'] != undefined ){
				this.customerReviews = this.result['custReviews']['customers'];
			} 
			if ( this.result['custReviews']['rating'] != undefined ){
				this.customerRating = this.result['custReviews']['rating'];
			} 
		}
		  
        });
	
	}
	
	ngOnDestroy() {
        this.subscription.unsubscribe();
    }
	selectReview(n){
		switch(n){
			case 1: this.reviewSelected = [1]; this.reviewRemain = [1,2,3,4]; break;
			case 2: this.reviewSelected = [1,2]; this.reviewRemain = [1,2,3]; break;
			case 3: this.reviewSelected = [1,2,3]; this.reviewRemain = [1,2]; break;
			case 4: this.reviewSelected = [1,2,3,4]; this.reviewRemain = [1]; break;
			default: this.reviewSelected = [1,2,3,4,5]; this.reviewRemain = [];
		}
	}
	
	unselectReview(n){
		n = n + this.reviewSelected.length;
		switch(n){
			case 1: this.reviewSelected = [1]; this.reviewRemain = [1,2,3,4]; break;
			case 2: this.reviewSelected = [1,2]; this.reviewRemain = [1,2,3]; break;
			case 3: this.reviewSelected = [1,2,3]; this.reviewRemain = [1,2]; break;
			case 4: this.reviewSelected = [1,2,3,4]; this.reviewRemain = [1]; break;
			default: this.reviewSelected = [1,2,3,4,5]; this.reviewRemain = [];
		}
	}
	
	getReviews(page) {
		this.reviewsLoader = 'Loading...';
		this.products.getProductReviews(this.result.id, page).subscribe(
            res => {
				if(res.data.total > 0){
					for( let i=0; i<res.data.total; i++ ){
						this.reviewsList.push(res.data.reviews[i]);
					}					
				}
				this.moreReviewsFlag = res.data.viewMore;
				this.reviewsLoader   = '';
            },
            (err: HttpErrorResponse) => {
                if(err.error instanceof Error){
                    console.log('Client Error: '+err.error.message);
                }else{
                    console.log(`Server Error: ${err.status}, body was: ${JSON.stringify(err.error)}`);
                }
            }
        );
	}
	
	loadMoreReviews(){
		this.reviewsPage = this.reviewsPage + 1;
		this.getReviews(this.reviewsPage);
	}
	
	getFinalRating(num:number):any{
		return Array.from(Array(num).keys());
	}
	
	getRemainingRating(num){
		num = 5 - num;
		return Array.from(Array(num).keys());
	}
	
	addReview(formData){
		this.reviewStatus = 1;
		this.reviewMsg = 'Wait...'
		formData.itemId = this.result.id;
		formData.rating = this.reviewSelected.length;
		if( this.userId > 0 ){
			//console.log(formData);
			this.customer.addReviews(formData).subscribe(
				res => {
					if(res.status){
						this.reviewStatus = 2;
						this.rForm = new FormGroup ({
							title: new FormControl('', Validators.compose([Validators.required]) ),
							description: new FormControl('', Validators.compose([Validators.required]) )
						});
						this.reviewSelected = [1,2,3,4,5];
						this.reviewRemain = [];
					}
					this.reviewMsg = res.message;
				},
				(err: HttpErrorResponse) => {
					this.reviewMsg = "Sorry, there are some app issue!";
				}
			);
		}else{
			this.router.navigate(['/customer/login'], {queryParams:{}});
		}
	}  
}
