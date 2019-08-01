import { Injectable } from '@angular/core';
import { HttpHeaders } from '@angular/common/http';

@Injectable({
	providedIn: 'root',
})

export class Myconfig {
    private myHost:string;
    public  apiEndPoint:string;

	public EMAIL_REGEXP:any;
	public MOBILE_REGEXP:any;
	public ALPHA_SPACE_REGEXP:any;
	public ALPHA_NUM_REGEXP:any;
	public ADDR_REGEXP:any;
	public DATE_REGEXP:any;
	public DATE_MM_DD_YYYY_REGEXP:any;
	public PINCODE_REGEXP:any;
	
	public SUBDIR:string;
    constructor(){
		if( window.location.hostname == 'www.perfumebooth.com' ){
			this.myHost = 'https://www.perfumebooth.com/pb/';
			this.SUBDIR = '/pg';
		}else if( window.location.hostname == 'www.perfumeoffer.com' ){
			this.myHost = 'https://www.perfumeoffer.com/pb/';
			this.SUBDIR = '/new/pg';
		}else{
			this.myHost = 'http://localhost/pb/';
			this.SUBDIR = '/new/pg';
		}
        this.apiEndPoint = this.myHost + 'us-api-v1.0/';

		this.EMAIL_REGEXP 			= /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		this.MOBILE_REGEXP 			= /^[1-9]{1}[0-9]{9}$/;
		this.ALPHA_SPACE_REGEXP 	= /^[a-zA-Z ]*$/;
		this.ALPHA_NUM_REGEXP 		= /^[a-zA-Z0-9 ]*$/;
		this.ADDR_REGEXP 			= /^[a-zA-Z0-9 )(,'/"._-]*$/;
		this.DATE_REGEXP 			= /^\d{4}-\d{2}-\d{2}$/;
		this.DATE_MM_DD_YYYY_REGEXP = /^\d{2}-\d{2}-\d{4}$/;
		this.PINCODE_REGEXP 		= /^\d{6}$/;
    }

    numToArray(num){
      return Array.from(Array(num).keys());
    }
	
	scrollToTop(horizontal:number=0, vertical:number=0){		
		//window.scroll(horizontal,vertical);
		(function smoothscroll(){
			var currentScroll = document.documentElement.scrollTop || document.body.scrollTop; 
			if (currentScroll > 0) 
			{
				window.requestAnimationFrame(smoothscroll);
				window.scrollTo(0, currentScroll - (currentScroll / 10));
			}
		})();		
	}


}
