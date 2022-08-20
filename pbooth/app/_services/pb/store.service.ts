import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from './customer.service';

import { Subject} from 'rxjs'; 

interface ItemsResponse {
  status:boolean,
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
    return this.http.post<ItemsResponse>(this.pbApi+'stores/get-active-cart?userId='+this.auth.getId(), JSON.stringify(formData));
  }

  addToCart(formData){
    return this.http.post<ItemsResponse>(this.pbApi+'stores/customer-cart?userId='+this.auth.getId(), JSON.stringify(formData));
  }

  updateCart(formData){
    return this.http.put<ItemsResponse>(this.pbApi+'stores/customer-cart?userId='+this.auth.getId(), JSON.stringify(formData));
  }

  removeCart(id){
    let prms = new HttpParams();
    prms = prms.set('id', `${id}`);
    return this.http.delete<ItemsResponse>(this.pbApi+'stores/customer-cart?userId='+this.auth.getId(), {params:prms});
  }

  checkPincode(pincode,address?){
    let prms = new HttpParams();
    prms = prms.set('pincode', pincode);
	if ( address != '' ) {
		prms = prms.set('address', address);
	}
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

  updateOrderDetails(formData){
    formData.userId = this.auth.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'stores/update-order-details-after-pg', JSON.stringify(formData));
  }

  getOrderStatus(formData){
    formData.userId = this.auth.getId();
    return this.http.post<{status: '', message: '', redirectUrl: '' }>(this.pbApi+'stores/get-order-status', JSON.stringify(formData));
  }

  pushOrderToVendors(formData){
    formData.userId = this.auth.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'stores/push-order-to-vendors', JSON.stringify(formData));
  }

}
