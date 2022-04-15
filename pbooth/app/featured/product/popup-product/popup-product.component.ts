import { Component, Input, OnInit, OnDestroy, ViewChild } from '@angular/core';
import { NguCarousel, NguCarouselStore } 	from '@ngu/carousel';
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
	
	//@ViewChild('carousel') carousel: NguCarousel;
    constructor(
		private dataService: DataService
	) { }

    ngOnInit() {
		this.subscription = this.dataService.getPopupProduct().subscribe(res => {
            this.result = res.items;
			this.nguInputs = {
				grid: {xs: 1, sm: 1, md: 1, lg: 1, all: 0},
				slide: 1,
				speed: 1000,
				interval: 2000,
				currentSlide: res.index,
				point: {
					visible: false,
					hideOnSingleSlide:true
				},
				load: 1,
				touch: true,
				easing: 'ease'
			};		
			//this.carousel.currentSlide = res.index;
			//console.log(this.carousel);
		
        });
    }
	
	initDataThirdFn(key: NguCarouselStore){
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
