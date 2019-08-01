import { Component, OnInit,ViewChild,ElementRef,HostListener } 	from '@angular/core';
import { Title, Meta, MetaDefinition,DomSanitizer } 			from '@angular/platform-browser';
import { NgStyle } 												from '@angular/common';
import { FormGroup, FormControl, Validators } 					from '@angular/forms';
import { Router, ActivatedRoute, NavigationEnd, Params } 		from '@angular/router';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { ProductsService } 										from './../../../_services/pb/products.service';
import { CustomerService } 										from '../../../_services/pb/customer.service';
import { StoreService } 										from './../../../_services/pb/store.service';
import { TrackingService } 										from './../../../_services/tracking.service';
import { ToastrService } 										from 'ngx-toastr';

@Component({
    selector: 'app-brand-perfume',
    templateUrl: './perfume.component.html',
    styleUrls: [
		'./perfume.component.css'
	]
})

export class BrandPerfumesComponent implements OnInit {
	
    userId:number			= 0;
    currentPath:string		= '';
    brandKey:string         = '';
	gender:string		= '';
	comboStatus:number		= 0;
	page:number				= 1;
	stopPageLoad:number		= 1;
    brands:any				= [];
    productList:any			= [];
	totalProduct:number		= 0;
	productId:number		= 0;
	notifymeMsg:string		= '';
	notifyStatus:number		= 0;
		
    findTitle:string		= '';
    brandTitle:string		= '';
    currentTitle:string		= '';
	resultMsg:string		= '';
	resultStatus:number		= 0;
	queryParams:any			= [];		
	winWidth:number			= 0;
	
	comboSection:number		= 0;
	
	topScrollClass:string 	= 'affix-top';
	@ViewChild('mainScreen') elementView: ElementRef;
	
	@HostListener('window:resize') onResize() {
		this.winWidth = window.innerWidth;
	}

	@HostListener('window:scroll') checkScroll() {
		const scrollPosition:number = window.pageYOffset;
		if( scrollPosition > 400 ){
			this.topScrollClass = 'affix';
		}else{
			this.topScrollClass = 'affix-top';
		}
		const componentPosition:number = this.elem.nativeElement.offsetTop;
		const componentHeight:number = this.elementView.nativeElement.offsetHeight;
		if( this.stopPageLoad && this.resultStatus && (this.productList.length > 0) && (scrollPosition > (componentHeight - componentPosition - 900) ) ){
			this.page += 1;
			this.getMoreProducts();
			//console.log(componentHeight);
		}
		//console.log(scrollPosition);
		//console.log((componentHeight - componentPosition - 900)+" : "+scrollPosition);
	}

	constructor(private toastr:ToastrService, private meta:Meta, private title: Title, private router: Router, private route: ActivatedRoute, private products: ProductsService, private auth: CustomerService, private store: StoreService,private config:Myconfig, private elem:ElementRef, private track:TrackingService) {
		this.userId = this.auth.getId();	
        this.route.url.subscribe( res => {
			this.currentPath 	= res[0].path;
			this.brandKey 		= res[1].path;
			this.currentTitle 	= this.toTitleCase(res[0].path.replace('-', ' '));			
			this.route.queryParams.subscribe((params: Params) => {
				this.queryParams = params;
				switch(params.gender){
					case 'male': this.gender = params.gender; break;
					case 'female': this.gender = params.gender; break; 
					default: this.gender = '';
				}
				this.getFilterProducts();
			});			
        });
		
	}
	
	ngOnInit(){
		this.config.scrollToTop();		
	}
	
	checkTest(n){
		this.toastr.success('Hello world!');
	}
    
    getFilterProducts(){
		this.productList	= [];
		this.resultMsg      = 'Loading...';
		this.totalProduct	= 0;
		this.resultStatus   = 0;
		this.page		    = 1;
		this.stopPageLoad   = 1;
		let siteTitle:string = 'Buy Online Perfume Fragrance | Perfume For Men and Women';
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		prms = prms.append('currentPath', `${this.currentPath}`);
		prms = prms.append('brandKey', `${this.brandKey}`);
		prms = prms.append('combo', `${this.comboStatus}`);
		
		for (let key in this.queryParams){
		  prms = prms.append(key, this.queryParams[key]);
		}
		
		this.products.getFilterProducts(prms).subscribe(
            res => {
                if(res.status){
					this.productList 			= res.data.products;
					this.totalProduct			= res.data.total;
					this.brands 				= res.data.brands;
					for(let item in this.brands){
						if(this.brands[item].url_key == this.brandKey){ 
							this.brandTitle = this.brands[item].title;
						}
					}
					
					this.comboSection = 0;
					for(let item in this.productList){
						if(this.productList[item].isCombo == "1"){ 
							this.comboSection = 1;
						}
					}
					if( 'all' == this.brandKey){ 
						this.brandTitle = 'All';
					}
				}
				if( this.productList.length < 12 ){
					this.stopPageLoad = 0;
				}
				this.resultMsg = res.message;
				this.title.setTitle(siteTitle);
				this.resultStatus = 1;
            },
            (err: HttpErrorResponse) => {
                if(err.error instanceof Error){
					this.resultMsg = err.error.message;
                }else{
					this.resultMsg = JSON.stringify(err.error);
                }
				this.resultStatus = 1;
            }
        );
    }
	
    getMoreProducts(){
		this.resultStatus = 0;
		let prms = new HttpParams();
		prms = prms.append('userId', `${this.userId}`);
		prms = prms.append('currentPath', `${this.currentPath}`);
		prms = prms.append('brandKey', `${this.brandKey}`);
		prms = prms.append('combo', `${this.comboStatus}`);
		prms = prms.append('gender', `${this.gender}`);
		prms = prms.append('page', `${this.page}`);
		
		for (let key in this.queryParams){
		  prms = prms.append(key, this.queryParams[key]);
		}
		
		this.products.getMoreProducts(prms).subscribe(
            res => {
				this.stopPageLoad = 0;
				for(let item in res){
					if(res[item].isCombo == "1"){ 
						this.comboSection = 1;
					}
					this.stopPageLoad = 1;
					this.productList.push(res[item]);
				}
				this.resultStatus = 1;
            },
            (err: HttpErrorResponse) => {
                if(err.error instanceof Error){
					this.resultMsg = err.error.message;
                }else{
					this.resultMsg = JSON.stringify(err.error);
                }
				this.resultStatus = 1;
            }
        );
    }
	
	addCart(itemId){
		localStorage.setItem('productId', itemId);
		if( this.userId > 0 ){
		  let formData:any = {itemId:itemId,quantity:1};
		  //console.log(formData);
		  this.store.addToCart(formData).subscribe(
			res => {
				if( res.status ){
					this.auth.setCart(res.data.cart);
					for(let i of this.productList){
						if( itemId == i.id ){ i.isCart = 1; }
					}
					this.toastr.success(res.message);
					let myCart:any = this.auth.getCart();
					for(let i=0; i < myCart.length; i++){
						if( myCart[i]['id'] == itemId ){
							this.track.addToCart(myCart[i]);
							break;
						}
					}
				}else{
					this.toastr.error(res.message);
				}
			},
			(err: HttpErrorResponse) => {
				this.toastr.error("Sorry, there are some app issue!");
			}
		  );
		}else{
		  //this.auth.getInActive();
		  this.router.navigate(['/customer/registration']);
		}
		
	}
  
	addToWishlist(itemId){
		if( this.userId > 0 ){
			let formData:any = {itemId:itemId};
			this.auth.addToWishlist(formData).subscribe(
				res => {
					alert(res.message);
				},
				(err: HttpErrorResponse) => {
					alert("Sorry, there are some app issue!");
				}
			);
		}else{
			this.router.navigate(['/customer/login']);
		}
	}
		
	selectGender(value:string){
		if( value != this.gender ){			
			this.gender = value;
			this.getFilterProducts(); 
		}
	}
	
	comboSelection(va){
		if( va != this.comboStatus ){
			this.comboStatus = va;
			this.getFilterProducts(); 
		}
	}
	
	goToCart(){ this.router.navigate(['/checkout/cart'], {}); }
	
	changeBrand(key){
		this.gender	= '';
		this.comboStatus 	= 0;
		this.router.navigate(['/'+this.currentPath+'/'+key], {});
	}
	
	notifyMePopup(itemId){
		this.productId    = itemId;
		this.notifyStatus = 0
		this.notifymeMsg  = '';
	}
	
	notifyMeSubmit(){
		this.notifyStatus = 1
		var email = this.elem.nativeElement.querySelector('#notifyemail').value;
		if( this.config.EMAIL_REGEXP.test(email) ){
			let formData:any = {productId:this.productId,email:email};
			this.products.notifyMe(formData).subscribe(
				res => {
					if( res.status ){
						this.notifyStatus = 0
						this.elem.nativeElement.querySelector('#notifyemail').value = '';
						this.elem.nativeElement.querySelector("#NotifyMeClose").click();
						this.notifymeMsg = '';
						this.toastr.success(res.message);
					}else{
						this.notifyStatus = 3
						this.notifymeMsg = res.message;
					}
				},
				(err: HttpErrorResponse) => {
					this.notifyStatus = 3
					this.notifymeMsg = 'Sorry, there are some app issue!';
				}
			);
		}else{
			this.notifyStatus = 3
			this.notifymeMsg = 'Please enter valid email id!';
		}
	}

	toTitleCase(str) {
		return str.replace(/\w\S*/g, function(txt){
			return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
		});
	}
}
