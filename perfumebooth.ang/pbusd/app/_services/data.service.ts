import { Injectable } from '@angular/core';
import { BehaviorSubject, Subject } from 'rxjs';

@Injectable({
	providedIn:'root'
})
export class DataService {
	public brands:any;
	private globalData = new BehaviorSubject<any>({headerClass:'',footerClass:''});
	updatedData = this.globalData.asObservable();
	private customDir = new BehaviorSubject<any>({customDir:''});
	customDirData = this.customDir.asObservable();
	
	private reviews = new BehaviorSubject<any>({id:0, reviews: [], progressRating: [] });
	private popupProduct = new BehaviorSubject<any>({index: 0, items: []});
	private relatedProduct = new BehaviorSubject<any>({userId: 0, items: []});
	private notifyme = new BehaviorSubject<any>({userId:0, productId: 0 });
	private deleteItem = new BehaviorSubject<any>({userId:0, cartId: 0 });
	
	constructor() {
		//console.log('data service');
	}
	
	changeDataAction(updatedData: any){
		this.globalData.next(updatedData);
	}
	
	clearReviews(){
		this.reviews.next({id:0, reviews: [], progressRating: [] });
	}
	
	sendReviews(param: any){
		this.reviews.next(param);
	}
	
	getReviews(){
		return this.reviews.asObservable();
	}
	
	clearPopupProduct(){
		this.popupProduct.next({index: 0, items: []});
	}
	
	sendPopupProduct(param: any){
		this.popupProduct.next(param);
	}
	
	getPopupProduct(){
		return this.popupProduct.asObservable();
	}
	
	clearRelatedProduct(){
		this.relatedProduct.next({userId: 0, items: []});
	}
	
	sendRelatedProduct(param: any){
		this.relatedProduct.next(param);
	}
	
	getRelatedProduct(){
		return this.relatedProduct.asObservable();
	}
	
	clearNotifyme(){
		this.notifyme.next({userId:0, productId: 0});
	}
	
	sendNotifyme(param: any){
		this.notifyme.next(param);
	}
	
	getNotifyme(){
		return this.notifyme.asObservable();
	}
	
	clearDeleteItem(){
		this.deleteItem.next({userId:0, cartId: 0});
	}
	
	sendDeleteItem(param: any){
		this.deleteItem.next(param);
	}
	
	getDeleteItem(){
		return this.deleteItem.asObservable();
	}
	
	changeCustomDirAction(value:any){
		this.customDir.next(value);
	}
	
	getBrands(){
		return {
			'perfume-brands':[
				{'href':'/english-blazer-perfume','image':'assets/images/brand/perfume/english.jpg','title':'English','alt':'English','index':1},
				{'href':'/chris-adams-perfume','image':'assets/images/brand/perfume/chris.jpg','title':'Chris Adams','alt':'Chris Adams','index':2},
				{'href':'/creation-perfume','image':'assets/images/brand/perfume/creation.jpg','title':'Creation','alt':'Creation','index':3},
				{'href':'/emper-perfume','image':'assets/images/brand/perfume/emper.jpg','title':'Emper','alt':'Emper','index':4},
				{'href':'/louis-cardin-perfume','image':'assets/images/brand/perfume/louis.jpg','title':'Louis Cardin','alt':'Louis Cardin','index':5},
				{'href':'/maryaj-perfume','image':'assets/images/brand/perfume/maryaj.jpg','title':'Maryaj','alt':'Maryaj','index':6},
				{'href':'/lomani-perfume','image':'assets/images/brand/perfume/lomani.jpg','title':'Lomani','alt':'Lomani','index':7},
				{'href':'/baug-sons-perfume','image':'assets/images/brand/perfume/baug.jpg','title':'Baug Sons','alt':'Baug Sons','index':8},
				{'href':'/perfumers-choice-perfume','image':'assets/images/brand/perfume/perfumers.jpg','title':'Perfumers Choice','alt':'Perfumers Choice','index':9},
				{'href':'/new-nb-perfume','image':'assets/images/brand/perfume/nb.jpg','title':'New NB','alt':'New NB','index':10},
				{'href':'/otoori-perfume','image':'assets/images/brand/perfume/otoori.jpg','title':'Otoori','alt':'Otoori','index':11},
				{'href':'/color-me-perfume','image':'assets/images/brand/perfume/color-me.jpg','title':'Color Me','alt':'Color Me','index':12}
			],
			'deodorant-brands':[
				{'href':'/english-blazer-deodorant','image':'assets/images/brand/deo/english.jpg','title':'English','alt':'English','index':1},
				{'href':'/chris-adams-deodorant','image':'assets/images/brand/deo/chris.jpg','title':'Chris Adams','alt':'Chris Adams','index':2},
				{'href':'/creation-deodorant','image':'assets/images/brand/deo/creation.jpg','title':'Creation','alt':'Creation','index':3},
				{'href':'/emper-deodorant','image':'assets/images/brand/deo/emper.jpg','title':'Emper','alt':'Emper','index':4},
				{'href':'/baug-sons-deodorant','image':'assets/images/brand/deo/baugsons.jpg','title':'Baug Sons','alt':'Baug Sons','index':5},
				{'href':'/color-me-deodorant','image':'assets/images/brand/deo/color.jpg','title':'Color Me','alt':'Color Me','index':6},
				{'href':'/jd-man-deodorant','image':'assets/images/brand/deo/jdman.jpg','title':'JD MAN','alt':'JD MAN','index':7},
				{'href':'/otoori-deodorant','image':'assets/images/brand/deo/otoori.jpg','title':'Otoori','alt':'Otoori','index':8},
				{'href':'/lomani-deodorant','image':'assets/images/brand/deo/lomani.jpg','title':'Lomani','alt':'Lomani','index':9},
				{'href':'/america-deodorant','image':'assets/images/brand/deo/america.jpg','title':'America','alt':'America','index':10},
				{'href':'/louis-cardin-deodorant','image':'assets/images/deo/louis.jpg','title':'Louis Cardin','alt':'Louis Cardin','index':11}
			],
			'top-deal-brands':[
				{'href':'/chris-adams-top-deals','image':'assets/images/brand/top_deals/chris.jpg','title':'Chris Adams','alt':'Chris Adams','index':1},
				{'href':'/color-me-top-deals','image':'assets/images/brand/top_deals/color-me.jpg','title':'Color Me','alt':'Color Me','index':2},
				{'href':'/jd-man-top-deals','image':'assets/images/brand/top_deals/jdman.jpg','title':'JD MAN','alt':'JD MAN','index':3},
				{'href':'/lomani-top-deals','image':'assets/images/brand/top_deals/lomani.jpg','title':'Lomani','alt':'Lomani','index':4},
				{'href':'/new-nb-top-deals','image':'assets/images/brand/top_deals/nb.jpg','title':'New NB','alt':'New NB','index':5},
				{'href':'/passion-top-deals','image':'assets/images/brand/top_deals/passion.jpg','title':'Passion','alt':'Passion','index':6},
				{'href':'/perfumers-choice-top-deals','image':'assets/images/brand/top_deals/perfumers.jpg','title':'Perfumers Choice','alt':'Perfumers Choice','index':7}
			]
		};
	}
}
