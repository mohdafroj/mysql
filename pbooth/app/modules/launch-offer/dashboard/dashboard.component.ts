import { Component, OnInit, HostListener, ElementRef, ViewEncapsulation, ViewChild } from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } from '@angular/platform-browser';
import { Router, ActivatedRoute, NavigationEnd } from '@angular/router';
import { HttpErrorResponse, HttpParams } from '@angular/common/http';
import { Myconfig } from './../../../_services/pb/myconfig';
import { ProductsService } from './../../../_services/pb/products.service';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
	sanitizer;
	pack1OriginalPrice = 599;
	pack2OriginalPrice = 1199;
	pack3OriginalPrice = 1799;
	pack1Price = 0;
	pack2Price = 0;
	pack3Price = 0;
	pack1Discount = '';
	pack2Discount = '';
	pack3Discount = '';
	maxDiscount = '';
	offerCoupon = '';
	resultStatus = 0;
	bannerWeb = '';
	bannerPhone = '';
	currentUri = '';
	clockDisplay = '';
	basePath = ['/','perfume-bottle','Perfumers-Club'];
	constructor (
		private config: Myconfig,
		private elem: ElementRef,
		private meta: Meta, 
		private title: Title, 
		private sanitize: DomSanitizer,
		private router: Router,
		private route: ActivatedRoute,
		private product: ProductsService
	) {
		this.sanitizer = sanitize;
	}

	ngOnInit() {
		/*let packPrices = this.config.packPrices;
		this.pack1Price = packPrices[0];
		this.pack2Price = packPrices[1];
		this.pack3Price = packPrices[2];*/
		this.config.scrollToTop();
		this.currentUri = this.router.url.split('/')[1];
		if ( this.currentUri == 'winter-sale-offer' ) {
			this.bannerWeb = 'winter_sale_offer_banner_web.jpg';
			this.bannerPhone = 'winter_sale_offer_banner_phone.jpg';
		} else {
			this.bannerWeb = 'main_banner_web.jpg';
			this.bannerPhone = 'main_banner.jpg';
		}
		this.route.queryParamMap.subscribe(res => {
			this.offerCoupon = (res['params']['coupon-code'] != undefined) ? res['params']['coupon-code'] : '';
		});  
		
		this.getLaunchOffer();
	}
	
	getLaunchOffer () {
		this.resultStatus   = 0;
		let prms = new HttpParams();
		prms = prms.append('page', 'launchoffer');
		prms = prms.append('offerCoupon', this.offerCoupon);
		this.product.getLaunchOffer(prms).subscribe(
            res => {
                if(res.status){
					this.pack1OriginalPrice = res.data['pack1']['cross'];
					this.pack2OriginalPrice = res.data['pack2']['cross'];
					this.pack3OriginalPrice = res.data['pack3']['cross'];
					this.pack1Price = res.data['pack1']['price'];
					this.pack2Price = res.data['pack2']['price'];
					this.pack3Price = res.data['pack3']['price'];
					this.pack1Discount = res.data['pack1']['discount'];
					this.pack2Discount = res.data['pack2']['discount'];
					this.pack3Discount = res.data['pack3']['discount'];
					this.maxDiscount = res.data['maxDiscount'];
					if ( res.data['offerCoupon'] && (res.data['offerCoupon'] != '') ) {
						let cartInfo = {};
						this.config.setOfferCoupon(res.data['offerCoupon']);
						let checkCartInfo = localStorage.getItem('cartInfo');
						if ( checkCartInfo != null ) {
							cartInfo = JSON.parse(checkCartInfo);
						}
						cartInfo['couponCode'] = res.data['offerCoupon'];
						cartInfo['optionStatus'] = 4;
						cartInfo['pbCashStatus'] = true;
						cartInfo['giftVoucherStatus'] = true;
						cartInfo['pbPointsStatus'] = false;
						cartInfo['trackPage'] = 'cart';
						//console.log(cartInfo);
						localStorage.setItem('cartInfo', JSON.stringify(cartInfo));
					}
					this.startOfferTimer();
					this.config.setMeta({title: res.data['meta']['title'], keywords: res.data['meta']['keywords'], description: res.data['meta']['description']});
				}
				this.resultStatus = 1;
            },
            (err: HttpErrorResponse) => {
                if(err.error instanceof Error){
					//this.resultMsg = err.error.message;
                }else{
					//this.resultMsg = JSON.stringify(err.error);
                }
				this.resultStatus = 1;
            }
        );
    }
	
	getOffer (price) {
		this.router.navigate(this.basePath, {queryParams: {price:price}});
	}
	
	startOfferTimer () {
		let abc = this.config.stopSale();
		if ( abc != "" ) {
			this.clockDisplay = abc;
		} else {
			this.config.setOfferCoupon('');
		}
		setTimeout( () => { this.startOfferTimer(); }, 1000);
	}
	
	
}
