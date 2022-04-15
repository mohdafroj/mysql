import { Component, OnInit, ElementRef, OnDestroy } from '@angular/core';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { ProductsService } from './../../../_services/pb/products.service';
import { DataService } from './../../../_services/data.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'notifyme',
  templateUrl: './notifyme.component.html',
  styleUrls: ['./notifyme.component.css']
})
export class NotifymeComponent implements OnInit, OnDestroy {
    notifymeMsg				= '';
    notifymeClass			= '';
	userId = 0;
	productId = 0;
	subscription: Subscription;
  constructor(
    private elem: ElementRef,
	private products: ProductsService,
	private dataService: DataService
  ) { }

  ngOnInit() {	  
		this.subscription = this.dataService.getNotifyme().subscribe(res => {
          if ( res ) {
            this.userId = res.userId;
            this.productId = res.productId;
			//console.log(res);
          }		  
        });
	  
  }

    notifyMeSubmit() {
		let EMAIL_REGEXP = /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
        this.notifymeClass = 'loader_msz';
        const email = this.elem.nativeElement.querySelector('#notifyemail').value;
        if ( EMAIL_REGEXP.test(email) ) {
            const formData: any = {productId: this.productId, email: email};
            this.products.notifyMe(formData).subscribe(
                res => {
                    if ( res.status ) {
                        this.notifymeClass = 'success_msz';
                    } else {
                        this.notifymeClass = 'error_msz';
                    }
                    this.notifymeMsg = res.message;
                },
                (err: HttpErrorResponse) => {
                    this.notifymeClass = 'error_msz';
                    this.notifymeMsg = 'Sorry, there are some app issue!';
                }
            );
        } else {
            this.notifymeClass = 'error_msz';
            this.notifymeMsg = 'Please enter valid email id!';
        }
    }
	
	ngOnDestroy() {
        this.subscription.unsubscribe();
    }

}
