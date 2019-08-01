import { Injectable } from '@angular/core';
import { HttpClient, HttpParams, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import { Myconfig } from './myconfig';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

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
	  //console.log("auth services called");
  }

  signUp(user) {
    return this.http.post<ItemsResponse>(this.pbApi+'customers/account', JSON.stringify(user));
  }

  signIn(user):Observable<ItemsResponse> {
    return this.http.put<ItemsResponse>(this.pbApi+'customers/account', JSON.stringify(user));
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

  
  getId(){
    let id:number = 0;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      id = user.id ? user.id:0;
    }
    return id;
  }
  
  getEmail(){
    let email:string;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      email = user.email ? user.email:'';
    }else{
      email='';
    }
    return email;
  }
  
  getFirstName(){
    let firstname:string;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      firstname = user.firstname ? user.firstname:'';
    }else{
      firstname='';
    }
    return firstname;
  }
  
  getLastName(){
    let lastname:string;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      lastname = user.lastname ? user.lastname:'';
    }else{
      lastname='';
    }
    return lastname;
  }
  
  getName(){
    let name:string = '';
    if( localStorage.getItem('usduser') ){
		let user:any = localStorage.getItem('usduser');
		user = JSON.parse(user);
		name = user.firstname ? user.firstname:'';
		if( name != '' ){
			name = user.lastname ? name+' '+user.lastname:'';
		}else{
			name = user.lastname ? user.lastname:'';
		}
    }
    return name;
  }
  
  getImage(){
    let a:string = 'assets/images/default-profile.jpg';
    if( localStorage.getItem('usduser') ){
		let user:any = localStorage.getItem('usduser');
		user = JSON.parse(user);
		if(user['image']){
			a = user.image;
		}
    }
    return a;
  }
  
  getMobile(){
    let mobile:any;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      mobile = user.mobile ? user.mobile:'';
    }else{
      mobile='';
    }
    return mobile;
  }
  
  getAddress(){
    let address:string;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      address = user.address ? user.address:'';
    }else{
      address='';
    }
    return address;
  }
  
  getCity(){
    let city:string;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      city = user.city ? user.city:'';
    }else{
      city='';
    }
    return city;
  }
  
  getPincode(){
    let pincode:any;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      pincode = user.pincode ? user.pincode:'';
    }else{
      pincode='';
    }
    return pincode;
  }
  
  getLocationId(){
    let locationId:number;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      locationId = user.location_id ? user.location_id:33;
    }else{
      locationId=33;
    }
    return locationId;
  }
  
  getToken(){
    let str:string;
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
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
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      prive = user['member']['status'] ? user['member']['status']:0;
    }
    return prive;
  }
  
  getCart(){
    let cart:any = [];
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      cart = user['cart'] ? user['cart']:[];
    }
    return cart;
  }
  
  setCart(cart){ //console.log(cart, "outer");
    if( localStorage.getItem('usduser') ){
      let user:any = localStorage.getItem('usduser');
      user = JSON.parse(user);
      user['cart'] = cart;
	  localStorage.setItem('usduser', JSON.stringify(user));
    }
    return true;
  }
  
  
}
