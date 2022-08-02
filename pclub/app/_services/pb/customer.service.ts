import { Injectable } from '@angular/core';
import { HttpClient, HttpParams, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import { Myconfig } from './myconfig';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { StoreService } from './store.service';

interface ItemsResponse {
  status:boolean,
  message:string,
  data:any
}

@Injectable({
	providedIn:'root'
})
export class CustomerService {
  pbApi:string;
    
  constructor( private config: Myconfig, private http: HttpClient, private router: Router) {
    this.pbApi = config.apiEndPoint;
  }

  otpSelector() {
    return this.http.get<ItemsResponse>(this.pbApi+'customers/otp-selector', {});
  }
  
  signUp(user) {
    return this.http.post<ItemsResponse>(this.pbApi+'customers/account', JSON.stringify(user));
  }

  signIn(user):Observable<ItemsResponse> {
    return this.http.put<ItemsResponse>(this.pbApi+'customers/account', JSON.stringify(user));
  }

  referCustomer(formData):Observable<ItemsResponse> {
    return this.http.post<ItemsResponse>(this.pbApi+'customers/get-refer', JSON.stringify(formData));
  }

  forgotPassword(user):Observable<ItemsResponse> {
    return this.http.post<ItemsResponse>(this.pbApi+'customers/forgot', JSON.stringify(user));
  }

  getProfile() {
    let prms = new HttpParams();
    let userId:number = this.getId();
    prms = prms.set('userId', `${userId}`);
    return this.http.get<ItemsResponse>(this.pbApi+'customers/profile', {params:prms});
  }

  updateProfile(formData) {
    let prms = new HttpParams();
    let userId:number = this.getId();
    prms = prms.set('userId', `${userId}`);
    return this.http.put<ItemsResponse>(this.pbApi+'customers/profile', JSON.stringify(formData), {params:prms});
  }

  updatePicture(formData) {
    formData.append('userId',this.getId());
    let headers = new HttpHeaders();
    //headers = headers.set('Content-Type', 'multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW');
    headers = headers.set('Enctype', 'application/x-www-form-urlencoded');
    return this.http.post<ItemsResponse>(this.pbApi+'customers/update-picture', formData, {headers: headers});
  }

  getAddresses() {
    let prms = new HttpParams();
    let userId:number = this.getId();
    prms = prms.set('userId', `${userId}`);
    return this.http.get<ItemsResponse>(this.pbApi+'customers/addresses', {params:prms});
  }

  addAddresses(formData) {
    let prms = new HttpParams();
    let userId:number = this.getId();
    prms = prms.set('userId', `${userId}`);
    return this.http.post<ItemsResponse>(this.pbApi+'customers/addresses', JSON.stringify(formData), {params:prms});
  }

  setDefaultAddress(formData) {
    let prms = new HttpParams();
    let userId:number = this.getId();
    prms = prms.set('userId', `${userId}`);
    return this.http.put<ItemsResponse>(this.pbApi+'customers/addresses', JSON.stringify(formData), {params:prms});
  }

  deleteAddress(formData) {
    let prms = new HttpParams();
    let userId:number = this.getId();
    prms = prms.set('userId', `${userId}`);
    return this.http.request<ItemsResponse>('delete', this.pbApi+'customers/addresses', {params:prms, body:formData});
  }

  getOrders(prms) {
    return this.http.get<ItemsResponse>(this.pbApi+'customers/get-orders', {params:prms});
  }
  
  getOrderDetails(formData) {
    formData.userId = this.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'customers/get-order-details', JSON.stringify(formData));
  }
  
  reOrder(formData) {
    formData.userId = this.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'customers/reorder', JSON.stringify(formData));
  }

  cancelOrder(formData) {
    formData.userId = this.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'customers/cancel-order', JSON.stringify(formData));
  }
  
  redeemOrder(formData){
    formData.userId = this.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'customers/redeem-order', JSON.stringify(formData));
  }

  getWishlist() {
    let prms = new HttpParams();
    let userId:number = this.getId();
    prms = prms.set('userId', `${userId}`);
    return this.http.get<ItemsResponse>(this.pbApi+'customers/wishlist', {params:prms});
  }

  addToWishlist(formData) {
    return this.http.post<ItemsResponse>(this.pbApi+'customers/wishlist?userId='+this.getId(), JSON.stringify(formData));
  }

  updateWishlist(formData) {
    return this.http.put<ItemsResponse>(this.pbApi+'customers/wishlist?userId='+this.getId(), JSON.stringify(formData));
  }

  getWalletTransactions(prms) {
    return this.http.get<ItemsResponse>(this.pbApi+'customers/get-wallet-transactions', {params:prms});
  }
  
  getWalletDetails(prms) {
    return this.http.get<ItemsResponse>(this.pbApi+'customers/get-wallet-details', {params:prms});
  }
  
  getCustomerReviews(page:number) {
    let prms = new HttpParams();
    let userId:number = this.getId();
    prms = prms.set('userId', `${userId}`);
    prms = prms.set('page', `${page}`);
    return this.http.get<ItemsResponse>(this.pbApi+'customers/reviews', {params:prms});
  }

  addReviews(formData) {
    formData.userId = this.getId();
    return this.http.post<ItemsResponse>(this.pbApi+'customers/reviews', JSON.stringify(formData));
  }


  updateSecurity(formData) {
    formData.userId = this.getId();
    return this.http.put<ItemsResponse>(this.pbApi+'customers/update-security', JSON.stringify(formData));
  }

  updateNewsletterStatus(formData) {
    formData.userId = this.getId();
    return this.http.put<ItemsResponse>(this.pbApi+'customers/update-newsletter-status', JSON.stringify(formData));
  }

  getRferEarn() {
    let prms = new HttpParams();
    let userId = this.getId();
    prms = prms.set('userId', `${userId}`);
    return this.http.get<ItemsResponse>(this.pbApi+'customers/get-refer-earn', {params:prms});
  }
  sendRferEarn(formData) {
    return this.http.post<ItemsResponse>(this.pbApi+'customers/get-refer-earn?userId='+this.getId(), JSON.stringify(formData));
  }
	customerLogout () {
		let code = '$2y$10$2kH8FyNLmmt3ZRQ7N6q1fOMZw'+this.getId()+'.OnpBadxmZ79oGwl.cyDm0f1Nijm';
		localStorage.clear();
		//this.router.navigate(['/login'],{queryParams:{ customerstatus:1, code: code, message:'welcome dear'}});
		this.router.navigate(['/login']);
	}
	
  getId(){
    let id:number = 0;
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      id = user.id ? user.id:0;
    }
    return id;
  }
  
  getEmail(){
    let email:string;
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      email = user.email ? user.email:'';
    }else{
      email='';
    }
    return email;
  }
  
  getFirstName(){
    let firstname = '';
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      firstname = user.firstname ? user.firstname:'';
    }
    return firstname;
  }
  
  getLastName(){
    let lastname = '';
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      lastname = user.lastname ? user.lastname:'';
    }
    return lastname;
  }
  
  getName(){
    let name = '';
    if( localStorage.getItem('pcuser') ){
		let user:any = localStorage.getItem('pcuser');
		user = JSON.parse(user);
		name = user.firstname ? user.firstname:'';
		if( name != '' ){
			name = user.lastname ? name+' '+user.lastname : name;
		} else {
			name = user.lastname ? user.lastname : '';
		}
    }
    return name;
  }
  
  getImage(){
    let a = 'assets/images/user_profile/user_profile.png';
    if( localStorage.getItem('pcuser') ){
		let user = localStorage.getItem('pcuser');
		user = JSON.parse(user);
		if ( user['image'] && (user['image'] != '') ) {
			a = user['image'];
		}
    }
    return a;
  }
  
  setImage(imageLink){
    let doAction = 0;
    if( localStorage.getItem('pcuser') ){
		let user = localStorage.getItem('pcuser');
		user = JSON.parse(user);
		if (user['image']) {
			user['image'] = imageLink;
		} else {
			user['image'] = imageLink;
		}
		doAction = 1;
		localStorage.setItem('pcuser', JSON.stringify(user));
    }
    return doAction;
  }
  
  getMobile(){
    let mobile:any;
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      mobile = user.mobile ? user.mobile:'';
    }else{
      mobile='';
    }
    return mobile;
  }
  
  getAddress(){
    let address = '';
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      address = user.address ? user.address:'';
    }
    return address;
  }
  
  getCity(){
    let city = '';
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      city = user.city ? user.city:'';
    }
    return city;
  }
  
  getPincode(){
    let pincode:any;
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      pincode = user.pincode ? user.pincode:'';
    }else{
      pincode='';
    }
    return pincode;
  }
  
  getLocationId(){
    let locationId:number;
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      locationId = user.location_id ? user.location_id:33;
    }else{
      locationId=33;
    }
    return locationId;
  }
  
  getToken(){
    let str:string;
    if( localStorage.getItem('pcuser') ){
      let user:any = localStorage.getItem('pcuser');
      user = JSON.parse(user);
      str = user.api_token ? user.api_token:'';
    }else{
      str = '';
    }
	str = 'Bearer '+str;
    return str;
  }

  getPrive(){
    let prive:number = 0;
	let pcuser = localStorage.getItem('pcuser');
    if( pcuser ){
      pcuser = JSON.parse(pcuser);	  
      prive = ( pcuser['member'] && pcuser['member']['status'] ) ? pcuser['member']['status'] : 0;
    }
    return prive;
  }

  
	getCart(){
		let cart = [];
		let pcuser = localStorage.getItem('pcuser');
		if( pcuser ){
			pcuser = JSON.parse(pcuser);
			cart = pcuser['shopping']['cart'] ? pcuser['shopping']['cart'] : [];
		}
		return cart;
	}
  
	setCart(cart){
		let doAction = -1;
		let pcuser = localStorage.getItem('pcuser');
		if( pcuser ){
			pcuser = JSON.parse(pcuser);
			pcuser['shopping']['cart'] = cart;
			localStorage.setItem('pcuser', JSON.stringify(pcuser));
		}
		return true;
	}
	
	getAccount() {
		var user = {};
		var pcuser = localStorage.getItem('pcuser');
		if ( pcuser ) {
			user = JSON.parse(pcuser);
		}
		return user;
	}

	setAccount (user) { // pass user data as json object format
		var pcuser = {};
		let checkUser = localStorage.getItem('pcuser');
		if( checkUser ){
			pcuser = JSON.parse(checkUser);
			let shopping = pcuser['shopping'] ? pcuser['shopping'] : {};
			shopping['cart'] = shopping['cart'] ? shopping['cart'] : [];
			shopping['pack'] = shopping['pack'] ? shopping['pack'] : [];
			pcuser = user;
			pcuser['shopping'] = shopping;
		} else {
			pcuser = user;
			pcuser['shopping'] = {cart: [], pack: []};
		}
		localStorage.setItem('pcuser', JSON.stringify(pcuser));  
		return 1;
	}
		
	doEmptyCart () {
		var pcuser = {};
		var checkUser = localStorage.getItem('pcuser');
		if( checkUser ){
			pcuser = JSON.parse(checkUser);
			pcuser['shopping'] = {cart: [], pack: []};
		}
		localStorage.setItem('pcuser', JSON.stringify(pcuser));		
		return true;
	}
  
	getFromCart () {
		var pcuser = {};
		var checkUser = localStorage.getItem('pcuser');
		if( checkUser ){
			pcuser = JSON.parse(checkUser);
			if ( pcuser['shopping'] ) {
				pcuser['shopping']['cart'] = pcuser['shopping']['cart'] ? pcuser['shopping']['cart'] : [];
				pcuser['shopping']['pack'] = pcuser['shopping']['pack'] ? pcuser['shopping']['pack'] : [];				
			} else {
				pcuser['shopping'] = {cart: [], pack: []};
			}
		} else {
			pcuser['shopping'] = {cart: [], pack: []};
		}
		localStorage.setItem('pcuser', JSON.stringify(pcuser));		
		return pcuser;
	}
  
	addIntoCart (product) {
		//console.log(product);
		product.cart_quantity = 1;
		let doAction = -1;
		let pcuser = localStorage.getItem('pcuser');
		if( pcuser ){
			pcuser = JSON.parse(pcuser);
			pcuser['shopping'] = pcuser['shopping'] ? pcuser['shopping'] : {};
			pcuser['shopping']['cart'] = pcuser['shopping']['cart'] ? pcuser['shopping']['cart'] : [];
			pcuser['shopping']['pack'] = pcuser['shopping']['pack'] ? pcuser['shopping']['pack'] : [];			
			if ( product.pack == undefined ) { // check product type is single
				if ( pcuser['shopping']['cart'].some( (item) => { return product.id == item.id; }, product.id) ) {
					doAction = 0;
				} else {
					pcuser['shopping']['cart'].push(product);
					doAction = 1;
				}
			} else { // check product type is package
				if ( pcuser['shopping']['pack'].some( (item) => { return product.pack == item.pack; }, product.pack) ) {
					doAction = 0;
				} else {
					pcuser['shopping']['pack'].push(product);
					doAction = 1;
				}
			}
		} else {
			pcuser['shopping'] = {cart: [], pack: []};
			// check product type is single
			if ( product.id && ( product.id  > 0 ) ) {
				doAction =  ( pcuser['shopping']['cart'].push(product) ) ? 1 : -1; 
				let formData = {
					itemId: product.id,
					quantity: 1
				};
			} else {
				doAction =  ( pcuser['shopping']['pack'].push(product) ) ? 1 : -1; 
			}
		}
		if ( doAction == 1 ) {
			localStorage.setItem('pcuser', JSON.stringify(pcuser));
		}
		return doAction;
	}
	
	updateQuantityInCart (product) { // object type {shopping: 'cart', quantity: 12, index: 1}
		let doAction = 0;
		let pcuser = localStorage.getItem('pcuser');
		if( pcuser ){
			pcuser = JSON.parse(pcuser);
			pcuser['shopping'] = pcuser['shopping'] ? pcuser['shopping'] : {};
			pcuser['shopping']['cart'] = pcuser['shopping']['cart'] ? pcuser['shopping']['cart'] : [];
			pcuser['shopping']['pack'] = pcuser['shopping']['pack'] ? pcuser['shopping']['pack'] : [];
			switch ( product.shopping ) {
				case 'cart':
					pcuser['shopping']['cart'][product.index]['cart_quantity'] = ( product.quantity > 0 ) ? product.quantity : 1;
					doAction = 1;
					break;
				case 'pack':
					pcuser['shopping']['pack'][product.index]['cart_quantity'] = ( product.quantity > 0 ) ? product.quantity : 1;
					doAction = 1;
					break;
				default:
			}
			
		}
		if ( doAction ) {
			localStorage.setItem('pcuser', JSON.stringify(pcuser));
		}
		return doAction;
	}
	
	removeFromCart (product) {
		let doAction = 0;
		let pcuser = localStorage.getItem('pcuser');
		if( pcuser ){
			pcuser = JSON.parse(pcuser);
			pcuser['shopping'] = pcuser['shopping'] ? pcuser['shopping'] : {};
			pcuser['shopping']['cart'] = pcuser['shopping']['cart'] ? pcuser['shopping']['cart'] : [];
			pcuser['shopping']['pack'] = pcuser['shopping']['pack'] ? pcuser['shopping']['pack'] : [];			
			if ( product.pack == undefined ) { // check product type is single
				pcuser['shopping']['cart'] = pcuser['shopping']['cart'].filter( (item) => { return product.id != item.id; }, product.id );
			} else { // check product type is package
				pcuser['shopping']['pack'] = pcuser['shopping']['pack'].filter( (item) => { return product.pack != item.pack; }, product.pack );
			}
			doAction = 1;
		}
		if ( doAction == 1 ) {
			localStorage.setItem('pcuser', JSON.stringify(pcuser));
		}
		return doAction;
	}
}
