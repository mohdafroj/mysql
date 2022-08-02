import { Injectable } from '@angular/core';
import { HttpHeaders } from '@angular/common/http';
import { Title, Meta, MetaDefinition } from '@angular/platform-browser';

@Injectable({
	providedIn: 'root',
})

export class Myconfig {
    private myHost:string;
    public apiEndPoint:string;
	public paymentGatewayUrl;
	public EMAIL_REGEXP:any;
	public MOBILE_REGEXP:any;
	public ALPHA_SPACE_REGEXP:any;
	public ALPHA_NUM_REGEXP:any;
	public ADDR_REGEXP:any;
	public DATE_REGEXP:any;
	public DATE_MM_DD_YYYY_REGEXP:any;
	public PINCODE_REGEXP:any;
	public cartCurrency = "₹";
	public headingImage = 'assets/images/heading_img.png';
	public packPrices = [499, 799, 1599];
	public headerHiddenPages = ["buynow"];
	public footerHiddenPages = ['checkout', 'cart', 'onepage', 'login', 'registration', 'launch-offer', 'winter-sale-offer'];
	public layoutOne = 'shopnow';	
	public SUBDIR:string;
	public homeUrl = '';
	public baseUrl = '';
	public otpSelectorMessage = 'Please enter email or phone number';
	public otpSelectorStatus = 0;
	public metaInfo = {};
	public categories;
    constructor(
		private meta: Meta, 
		private title: Title
	){
		let tempHost = window.location.hostname;
		this.baseUrl = location.protocol+'//'+tempHost;
		this.paymentGatewayUrl = this.baseUrl+'/pb/stores/payment-request';
		if( ["www.perfumebooth.com", "perfumebooth.com"].indexOf(tempHost) != -1 ) {
			this.myHost = location.protocol+'//www.perfumebooth.com/pb/api/';
		} else if( ["www.perfumersclub.com", "perfumersclub.com"].indexOf(tempHost) != -1 ){
			this.myHost = location.protocol+'//www.perfumersclub.com/pb/subscription-api-v1.0/';
		} else if( ["www.perfumeoffer.com", "perfumeoffer.com"].indexOf(tempHost) != -1 ){
			this.myHost = location.protocol+'//www.perfumeoffer.com/pb/api/';
		} else if( ["dev.perfumersclub.com"].indexOf(tempHost) != -1 ){
			this.myHost = location.protocol+'//dev.perfumersclub.com/pb/subscription-api-v1.0/';
		} else {
			this.myHost = 'http://localhost/pb/subscription-api-v1.0/';
		}		
        this.apiEndPoint = this.myHost;

		this.EMAIL_REGEXP 			= /^[_a-z0-9]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
		this.MOBILE_REGEXP 			= /^[1-9]{1}[0-9]{9}$/;
		this.ALPHA_SPACE_REGEXP 	= /^[a-zA-Z ]*$/;
		this.ALPHA_NUM_REGEXP 		= /^[a-zA-Z0-9 ]*$/;
		this.ADDR_REGEXP 			= /^[a-zA-Z0-9 )(,'/"._-]*$/;
		this.DATE_REGEXP 			= /^\d{4}-\d{2}-\d{2}$/;
		this.DATE_MM_DD_YYYY_REGEXP = /^\d{2}-\d{2}-\d{4}$/;
		this.PINCODE_REGEXP 		= /^\d{6}$/;
		this.metaInfo = {
			'home': {
				'title': 'Perfumer’s Club : Top Indian Perfume | Premium Scents',
				'keyword': 'Perfumes, Fragrances, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men',
				'description': 'Buy best perfumes for men and women of Indian perfume brands online at low price. Smell amazing with our unique and various kind of fragrance.'
			},
			'scent-match': {
				'title':'Scent Match | Original Branded Fragrance Shop',
				'keyword':'Original Branded Fragrance Shop, Scent Match, Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men  , Perfumes, Fragrances',
				'description':'Browse and match your dream fragrance and buy now original branded perfume on Perfumers Club. It is India’s leading online perfume store.',
			},
			'about-us': {
				'title': 'About Perfumers Club',
				'keyword': 'About Perfumers Club, Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men',
				'description': 'Know about perfumers club and its brand perfumes. It is high quality perfumes manufacturer and seller in India.'
			},
			'privacy-policy': {
				'title': 'Perfumers Club Privacy Policy',
				'keyword': 'Perfumers Club Privacy Policy, Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men',
				'description': 'Perfumers Club is and perfumer seller and follows all Indian standard privacy policy. If you want to know more click here.'
			},
			'terms-of-use': {
				'title': 'Perfumers Club Terms of Use',
				'keyword': 'Perfumers Club Terms of Use, Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men',
				'description': 'Read a perfumers club terms of use for online purchase and uses of website. It is leading online perfume retailer in India.'
			},
			'faq': {
				'title': 'Perfumers Club FAQ',
				'keyword': 'Perfumers Club FAQ, Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men',
				'description': 'Perfumers Club FAQ list have all type of customer query and its answer. Browse here and know your concern.'
			},
			'contact-us': {
				'title': 'Perfumers Club Contact Details',
				'keyword': 'Perfumers Club Contact Details, Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men',
				'description': 'Available Perfumers Club contact details here like customer care contact no. and email address. You have need to visit this page and contact them.'
			},
			'test': {
				'title': '',
				'keyword': '',
				'description': ''
			}
		};

		this.categories = {
			'category1': {'key':'LightR-3-Perfumes', 'title': 'Lightr + 3 Perfumes'},
			'category2': {'key':'Set-of-3X8ml-Perfumes', 'title': 'Set of 3X8ml Perfumes'},
			'category3': {'key':'Lightr-7X8ml-Perfumes', 'title': '7X8ml Perfumes'},
			'category4': {'key':'Lightr-7-Perfumes', 'title': 'Lightr + 7 Perfumes'},
			'category5': {'key':'Set-of-3X50ml-Perfumes', 'title': 'Set of 3X50ml Perfumes'},
			'category6': {'key':'Full-Size-50ml-Perfume', 'title': 'Full Size 50ml Perfume'}
		};
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

	scrollToBottom( h: number=0, v: number=0 ) {
		(function smoothscroll(h, v){
			window.scrollTo(h, v);
		})(h, v);
	}
	
	setMeta(param) {
		this.title.setTitle(param.title);
		let keyword: MetaDefinition = { name: 'keywords', content: param.keywords};
		this.meta.addTag(keyword,true);
		let description: MetaDefinition = { name: 'description', content: param.description};
		this.meta.addTag(description,true);
	}

	getOfferCoupon () {
		return sessionStorage.getItem("offerCoupon");
	}
	setOfferCoupon (coupon) {
		sessionStorage.setItem("offerCoupon", coupon); 
		let currentDate = this.getCurrentDate();
		sessionStorage.setItem("offerDate", currentDate);
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
		let offerDate = sessionStorage.getItem("offerDate");
		return ( offerDate != undefined ) ? offerDate : '';
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
