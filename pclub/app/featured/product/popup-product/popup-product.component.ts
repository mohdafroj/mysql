import { Component, Input, OnInit, OnDestroy, ViewChild } from '@angular/core';
import { Subscription } from 'rxjs';
import { DataService } from './../../../_services/data.service';

@Component({
  selector: 'popup-product',
  templateUrl: './popup-product.component.html',
  styleUrls: ['./popup-product.component.css']
})
export class PopupProductComponent implements OnInit, OnDestroy {
	subscription: Subscription;
    result = [];
	nguInputs;
	nguThirdToken: string;
	
    constructor(
		private dataService: DataService
	) { }

    ngOnInit() {
		this.subscription = this.dataService.getPopupProduct().subscribe(res => {
            this.result = res.items;
		
        });
    }
	
	initDataThirdFn(key: any){
		this.nguThirdToken = key.token;
		console.log(key);
	}
	
	getFinalRating(num: number): any {
		return Array.from(Array(num).keys());
	}
	
	getRemainingRating(num){
		num = 5 - num;
		return Array.from(Array(num).keys());
	}
	
	ngOnDestroy(){
		this.subscription.unsubscribe();
	}
	
}
