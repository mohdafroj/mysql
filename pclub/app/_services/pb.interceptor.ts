import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { HttpEvent, HttpResponse, HttpErrorResponse, HttpInterceptor, HttpHandler, HttpRequest } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, filter, tap } from 'rxjs/operators';
import { CustomerService } 	from './pb/customer.service';

@Injectable()
export class PBInterceptor implements HttpInterceptor {
	constructor( private router: Router, private customer: CustomerService ){ }
	intercept (request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
		var token: string;
		if( localStorage.getItem('pcuser') ){
			let user:any = localStorage.getItem('pcuser');
			user = JSON.parse(user);
			token = user.api_token || '*';
		}else{
			token = '*';
		}
		//token = this.customer.getToken();
		let headersObject  = {Authorization: `Bearer ${token}`, 'Accept': 'application/json'};
		if(request.url.split('/')[request.url.split('/').length-1] != 'update-picture'){
			headersObject['Content-Type']  = 'application/json';
		}
		request  = request.clone({
			setHeaders:headersObject
		});
		return next.handle(request).pipe(
			tap( (event:HttpEvent<any>) => {
			    if (event instanceof HttpResponse) {
				   //console.log(event);
			    }
		    }, 
		    (err: any) => {
				if (err instanceof HttpErrorResponse) {
					//localStorage.clear();
					//this.router.navigate(['/customer/login']);
				}
		    }));
	}
}


//sudo unzip na.zip -d /var/www/html/