import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';

interface ItemsResponse {
  status:boolean,
  message:string,
  data:any
}

@Injectable({
	providedIn:'root'
})
export class PagesService {
    pbApi:string;
    ext:string;
    constructor( private config: Myconfig, private http: HttpClient ) {
        this.pbApi = config.apiEndPoint;
        this.ext = config.linkExt;
		//console.log("page service called");
    }
    getAllCode(){
        let head:any;
        head = new HttpHeaders();
        head.set('Content-Type', 'application/json');        
        return this.http.get(this.pbApi+'zipcodes'+this.ext, {headers:head});
    }
    
    getCode(param){
        let head:any;
        head = new HttpHeaders();
        head.set('Content-Type', 'application/json');
        return this.http.get(this.pbApi+'zipcodes'+this.ext, {headers:head});
    }
	
    contactUs(formData){
		let headers = new HttpHeaders();
		headers = headers.set('Content-Type', 'application/json');
		return this.http.post<ItemsResponse>(this.pbApi+'pages/contact-us', JSON.stringify(formData), {headers: headers});
    }

    newsletterSubscribe(formData){
		let headers = new HttpHeaders();
		headers = headers.set('Content-Type', 'application/json');
		return this.http.post<ItemsResponse>(this.pbApi+'pages/newsletter-subscribe', JSON.stringify(formData), {headers: headers});
    }

}
