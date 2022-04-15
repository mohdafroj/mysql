import { Injectable } from '@angular/core';
import { HttpHeaders } from '@angular/common/http';
import { Title, Meta, MetaDefinition } from '@angular/platform-browser';

@Injectable({
	providedIn: 'root',
})

export class Myconfig {
    private myHost:string;
    private myApi:string;
    public  apiEndPoint:string;
    public  linkExt:string;
    public  head:any;


	public STATES:any;
	public EMAIL_REGEXP:any;
	public MOBILE_REGEXP:any;
	public ALPHA_SPACE_REGEXP:any;
	public ALPHA_NUM_REGEXP:any;
	public ADDR_REGEXP:any;
	public DATE_REGEXP:any;
	public DATE_MM_DD_YYYY_REGEXP:any;
	public PINCODE_REGEXP:any;
	public headerHiddenPages = [];
	public footerHiddenPages = ['cart','onepage','launch-offer', 'winter-sale-offer'];
	
	public SUBDIR:string;
    constructor(
		private meta: Meta, 
		private title: Title
	){
		this.SUBDIR = '/new/pg';
		if( window.location.hostname == 'www.perfumebooth.com' ){
			this.myHost = 'https://www.perfumebooth.com/pb/';
			this.SUBDIR = '/pg';
		}else if( window.location.hostname == 'www.perfumeoffer.com' ){
			this.myHost = 'https://www.perfumeoffer.com/pb/';
		}else if( window.location.hostname == 'dev.perfumersclub.com' ){
			this.myHost = location.protocol+'//dev.perfumersclub.com/pb/';
		}else{
			this.myHost = 'http://localhost/pb/';
		}
        this.myApi = 'api/';
        this.apiEndPoint = this.myHost + this.myApi;
        this.linkExt = '.json';

        let h = new HttpHeaders();
        h = h.set('Content-Type', 'application/json; charset=UTF-8');
        h = h.set('Accept', 'application/json');
        h = h.set('Authorization', '*');  //Authorization
        this.head = h;


		this.STATES = [{"id":34,"title":"Andaman and Nicobar Islands","code":"AN","lft":63,"rght":64,"parent_id":2,"is_active":"active"},{"id":3,"title":"Andhra Pradesh ","code":"AP","lft":3,"rght":4,"parent_id":2,"is_active":"active"},{"id":6,"title":"Arunachal Pradesh","code":"AR","lft":7,"rght":8,"parent_id":2,"is_active":"active"},{"id":7,"title":"Assam","code":"AS","lft":9,"rght":10,"parent_id":2,"is_active":"active"},{"id":4,"title":"Bihar","code":"BH","lft":5,"rght":6,"parent_id":2,"is_active":"active"},{"id":35,"title":"Chandigarh","code":"CH","lft":65,"rght":66,"parent_id":2,"is_active":"active"},{"id":29,"title":"Chhattisgarh","code":"CT","lft":53,"rght":54,"parent_id":2,"is_active":"active"},{"id":36,"title":"Dadra and Nagar Haveli","code":"DN","lft":67,"rght":68,"parent_id":2,"is_active":"active"},{"id":37,"title":"Daman and Diu","code":"DD","lft":69,"rght":70,"parent_id":2,"is_active":"active"},{"id":33,"title":"Delhi","code":"DL","lft":61,"rght":62,"parent_id":2,"is_active":"active"},{"id":8,"title":"Goa","code":"GA","lft":11,"rght":12,"parent_id":2,"is_active":"active"},{"id":9,"title":"Gujarat","code":"GJ","lft":13,"rght":14,"parent_id":2,"is_active":"active"},{"id":10,"title":"Haryana","code":"HR","lft":15,"rght":16,"parent_id":2,"is_active":"active"},{"id":11,"title":"Himachal Pradesh","code":"HP","lft":17,"rght":18,"parent_id":2,"is_active":"active"},{"id":12,"title":"Jammu and Kashmir","code":"JK","lft":19,"rght":20,"parent_id":2,"is_active":"active"},{"id":31,"title":"Jharkhand","code":"JH","lft":57,"rght":58,"parent_id":2,"is_active":"active"},{"id":13,"title":"Karnataka","code":"KA","lft":21,"rght":22,"parent_id":2,"is_active":"active"},{"id":14,"title":"Kerala","code":"KL","lft":23,"rght":24,"parent_id":2,"is_active":"active"},{"id":38,"title":"Lakshadweep","code":"LD","lft":71,"rght":72,"parent_id":2,"is_active":"active"},{"id":15,"title":"Madhya Pradesh","code":"MP","lft":25,"rght":26,"parent_id":2,"is_active":"active"},{"id":16,"title":"Maharashtra","code":"MH","lft":27,"rght":28,"parent_id":2,"is_active":"active"},{"id":17,"title":"Manipur","code":"MN","lft":29,"rght":30,"parent_id":2,"is_active":"active"},{"id":18,"title":"Meghalaya","code":"ME","lft":31,"rght":32,"parent_id":2,"is_active":"active"},{"id":19,"title":"Mizoram","code":"MI","lft":33,"rght":34,"parent_id":2,"is_active":"active"},{"id":20,"title":"Nagaland","code":"NL","lft":35,"rght":36,"parent_id":2,"is_active":"active"},{"id":21,"title":"Orissa","code":"OR","lft":37,"rght":38,"parent_id":2,"is_active":"active"},{"id":39,"title":"Puducherry","code":"PY","lft":73,"rght":74,"parent_id":2,"is_active":"active"},{"id":22,"title":"Punjab","code":"PB","lft":39,"rght":40,"parent_id":2,"is_active":"active"},{"id":23,"title":"Rajasthan","code":"RJ","lft":41,"rght":42,"parent_id":2,"is_active":"active"},{"id":24,"title":"Sikkim","code":"SK","lft":43,"rght":44,"parent_id":2,"is_active":"active"},{"id":25,"title":"Tamil Nadu","code":"TN","lft":45,"rght":46,"parent_id":2,"is_active":"active"},{"id":32,"title":"Telangana","code":"TS","lft":59,"rght":60,"parent_id":2,"is_active":"active"},{"id":26,"title":"Tripura","code":"TR","lft":47,"rght":48,"parent_id":2,"is_active":"active"},{"id":27,"title":"Uttar Pradesh","code":"UP","lft":49,"rght":50,"parent_id":2,"is_active":"active"},{"id":30,"title":"Uttarakhand","code":"UT","lft":55,"rght":56,"parent_id":2,"is_active":"active"},{"id":28,"title":"West Bengal","code":"WB","lft":51,"rght":52,"parent_id":2,"is_active":"active"}],

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
	

	isEmpty(obj){
		return JSON.stringify(obj) === JSON.stringify({});
	}

	setMeta(param) {
		this.title.setTitle(param.title);
		let keyword: MetaDefinition = { name: 'keywords', content: param.keywords};
		this.meta.addTag(keyword,true);
		let description: MetaDefinition = { name: 'description', content: param.description};
		this.meta.addTag(description,true);
	}
	
	setOfferCoupon (coupon) {
		localStorage.setItem("offerCoupon", coupon);
		let currentDate = this.getCurrentDate(); 
		localStorage.setItem("offerDate", currentDate);
		return 1;
	}
	
	getCurrentDate () {
		let obj = new Date();
		let d = obj.getDate();
		let m = obj.getMonth() + 1;
		let y = obj.getFullYear();
		let dd = ( d < 10 ) ? "0"+d : ""+d;
		let mm = ( m < 10 ) ? "0"+m : ""+m;
		let yy = y+"-"+mm+"-"+dd;
		return yy;
	}
	
	getOfferDate () {
		let offerDate = localStorage.getItem("offerDate");
		return ( offerDate != undefined ) ? offerDate : '';
	}
	
	getOfferCoupon () {
		let coupon = localStorage.getItem("offerCoupon");
		return ( coupon != undefined ) ? coupon : '';
	}
	
	getByKey (key) {
		let item = localStorage.getItem(key);
		return ( item != undefined ) ? JSON.parse(item) : '';
	}
	
	setByKey (key, item) {
		localStorage.setItem(key, JSON.stringify(item));
		return item;
	}
	
	getDeal() {
		let hours = "";
		let minutes = "";
		let seconds = "";
		let b = {hours:'00',minutes:'00',seconds:'00',active:0};
		let obj = new Date();
		let h = 23 - obj.getHours();
		let m = 59 - obj.getMinutes();
		let s = 60 - obj.getSeconds();
		if ( ( s < 2 ) && ( m == 0 ) && ( h == 0 ) ) {
			//this.setOfferCoupon('');
		} else {
			if ( h < 10 ) {
				hours =  "0" + h;
			} else {
				hours =  "" + h;
			}
			if ( m < 10 ) {
				minutes = "0" + m;
			} else {
				minutes = "" + m;
			}
			if ( s < 10 ) {
				seconds = "0" + s;
			} else {
				seconds = "" + s;
			}
			b = {hours:hours,minutes:minutes,seconds:seconds,active:1};
		}
		return b;
	}
	
	stopSale() {
		let hours = "";
		let minutes = "";
		let seconds = "";
		let b = "";
		let obj = new Date();
		let h = 23 - obj.getHours();
		let m = 59 - obj.getMinutes();
		let s = 60 - obj.getSeconds();
		if ( ( s < 2 ) && ( m == 0 ) && ( h == 0 ) ) {
			b = '';
			this.setOfferCoupon('');
		} else {
			if ( h < 10 ) {
				hours =  "0" + h;
			} else {
				hours =  "" + h;
			}
			if ( m < 10 ) {
				minutes = "0" + m;
			} else {
				minutes = "" + m;
			}
			if ( s < 10 ) {
				seconds = "0" + s;
			} else {
				seconds = "" + s;
			}
			b = hours + " : " + minutes + " : " + seconds;
			let coupon = this.getOfferCoupon();
			if ( coupon == '' ) {
				b = '';
			}
		}
		return b;
	}
	
	getSaleStrip() {
		let days = "0";
		let hours = "";
		let minutes = "";
		let seconds = "";
		let b = {};
		let obj = new Date();
		let h = 23 - obj.getHours();
		let m = 59 - obj.getMinutes();
		let s = 60 - obj.getSeconds();
		if ( ( s < 2 ) && ( m == 0 ) && ( h == 0 ) ) {
			this.setOfferCoupon('');
		} else {
			if ( h < 10 ) {
				hours =  "0" + h;
			} else {
				hours =  "" + h;
			}
			if ( m < 10 ) {
				minutes = "0" + m;
			} else {
				minutes = "" + m;
			}
			if ( s < 10 ) {
				seconds = "0" + s;
			} else {
				seconds = "" + s;
			}
			let coupon = this.getOfferCoupon();
			if ( coupon != '' ) {
				b['days'] = days;
				b['hours'] = hours;
				b['minutes'] = minutes;
				b['seconds'] = seconds;
			}
		}
		return b;
	}
	
}
