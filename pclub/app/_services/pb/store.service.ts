import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from './customer.service';

import { Subject} from 'rxjs'; 

interface ItemsResponse {
  status:number,
  message:string,
  data:any
}
interface PincodeResponse {
  status:number,
  message:string,
  data:any
}
@Injectable({
	providedIn:'root'
})
export class StoreService {
  pbApi:string;
	invokeEvent: Subject<any> = new Subject(); 
	
	constructor( private config: Myconfig, private auth: CustomerService, private http: HttpClient ) {
      this.pbApi = config.apiEndPoint;
		  //console.log("store services called");
    }
	
	callMethodOfSecondComponent(param) {
		  console.log("service called!");
      this.invokeEvent.next(param);
    }
	  
  getCart(formData){
	formData['offerCoupon'] = this.config.getOfferCoupon();
    return this.http.post<ItemsResponse>(this.pbApi+'stores/get-active-cart?userId='+this.auth.getId(), JSON.stringify(formData));
  }

  addToCart(formData){
	formData['offerCoupon'] = this.config.getOfferCoupon();
    return this.http.post<ItemsResponse>(this.pbApi+'stores/customer-cart?userId='+this.auth.getId(), JSON.stringify(formData));
  }

  updateCart(formData){
	formData['offerCoupon'] = this.config.getOfferCoupon();
    return this.http.put<ItemsResponse>(this.pbApi+'stores/customer-cart?userId='+this.auth.getId(), JSON.stringify(formData));
  }

  removeCart(id){
    let prms = new HttpParams();
    prms = prms.set('id', `${id}`);
    return this.http.delete<ItemsResponse>(this.pbApi+'stores/customer-cart?userId='+this.auth.getId(), {params:prms});
  }

  checkPincode(pincode){
    let prms = new HttpParams();
    prms = prms.set('pincode', pincode);
    return this.http.get<PincodeResponse>(this.pbApi+'stores/get-pincode', {params:prms});
  }

  getOtp(formData){
    return this.http.post<ItemsResponse>(this.pbApi+'stores/get-otp',JSON.stringify(formData));
  }

  verifyOtp(formData){
    return this.http.put<ItemsResponse>(this.pbApi+'stores/get-otp',JSON.stringify(formData));
  }

  saveOrderDetails(formData){
    formData.userId = this.auth.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'stores/create-order', JSON.stringify(formData));
  }

  getOrderStatus(formData){
    formData.userId = this.auth.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'stores/get-order-status', JSON.stringify(formData));
  }

  pushOrderToVendors(formData){
    formData.userId = this.auth.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'stores/push-order-to-vendors', JSON.stringify(formData));
  }
  
	getCartInfo () {
		var inputData = {};
		var checkCart = localStorage.getItem('pccartInfo');
		if ( checkCart && checkCart != "undefined" ) {
			inputData = JSON.parse(checkCart);
			inputData['couponCode'] = ["undefined", "null"].includes(inputData['couponCode']) ?  "" : inputData['couponCode'];
		} else {
			inputData = {
				paymentMethod:'',
				pincode:'',
				couponCode:'',
				trackPage: 'cart',
				optionStatus:1,
				points:0,
				cash:0,
				voucher:0,
				pbPrive:0
			};
		}
		var offerCoupon = this.config.getOfferCoupon();
		if ( offerCoupon && (offerCoupon != 'undefined') ) {
			inputData['couponCode'] = offerCoupon;
		}
		console.log(inputData, offerCoupon);
		return inputData;
	}

	setCartInfo ( item ) {
		if( localStorage.getItem('pccartInfo') ){
			localStorage.removeItem('pccartInfo');
		}
		localStorage.setItem('pccartInfo', JSON.stringify(item));
	}

	getTrackingData () {
		var item = {};
		var checkCart = localStorage.getItem('trackingData');
		if ( checkCart ) {
			item = JSON.parse(checkCart);
		}
		return item;
	}
	
	setTrackingData (item) {
		if( localStorage.getItem('trackingData') ){
			localStorage.removeItem('trackingData');
		}
		localStorage.setItem('trackingData',JSON.stringify(item));
	}
	
	getSuccessData () {
		var item = {};
		var checkCart = localStorage.getItem('pcSuccessData');
		if ( checkCart ) {
			item = JSON.parse(checkCart);
		}
		return item;
	}
	setSuccessData (item) {
		if( localStorage.getItem('pcSuccessData') ){
			localStorage.removeItem('pcSuccessData');
		}
		localStorage.setItem('pcSuccessData',JSON.stringify(item));
	}
}
