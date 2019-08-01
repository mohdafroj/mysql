import { Component, OnInit,ViewChild, ElementRef } 				from '@angular/core';
import { Location } 											from '@angular/common';
import { HttpErrorResponse } 									from '@angular/common/http';
import { Router,ActivatedRoute,NavigationEnd } 					from '@angular/router';
import { DataService } 											from './../../_services/data.service';
import { CustomerService } 										from '../../_services/pb/customer.service';
import { StoreService } 										from './../../_services/pb/store.service';
import { TrackingService } 										from './../../_services/tracking.service';

@Component({
  selector: 'header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

	@ViewChild('hideDeleteMiniCartModal') hideDeleteMiniCartModal: ElementRef;
	userId:number;
	cartId:number;
	confimMsg:string;	  
	toggledClass:string;
	buttonClass:string;	
	customDir:string;
	headerClass:string;
	priveMember:boolean = false;
	
	secondParam:string = '';
	thirdParam:string = '';
	constructor(private loc:Location,private data: DataService,private auth: CustomerService, private store: StoreService, private router: Router, private route: ActivatedRoute, private elem:ElementRef, private track:TrackingService) {
		this.userId 	  = 0;
		this.cartId 	  = 0;
		this.confimMsg	  = '';	  
		this.toggledClass = '';
		this.buttonClass  = '';	
		this.customDir  = '';
		this.headerClass  = '';
	}
	
	ngOnInit(){
		this.userId = this.auth.getId();
        this.router.events.subscribe(event => {
            if(event instanceof NavigationEnd) {
				this.toggledClass 		= '';
				this.buttonClass 		= '';
				this.secondParam 	= event.url.split('/')[1]; 
				this.thirdParam 	= event.url.split('/')[2];
				if( this.secondParam && this.secondParam.includes('?') ){
					this.secondParam 	= this.secondParam.split('?')[0];
				}
				if( this.thirdParam && this.thirdParam.includes('?') ){
					this.thirdParam 	= this.thirdParam.split('?')[0];
				}
				
            }
			
        });
		this.data.customDirData.subscribe(res => this.customDir = res.customDir);
		this.data.updatedData.subscribe(res => this.headerClass = res.headerClass);
		this.updateSiteLogo();
	}
	
	updateSiteLogo(){
		let prive:number = this.auth.getPrive();
		if( prive == 1 ){
			this.priveMember = true;
		}else{
			this.priveMember = false;
		}
		setTimeout(()=>{ this.updateSiteLogo(); }, 3000);
	}

	toggleMenu(){
		this.toggledClass = ( this.toggledClass == '' ) ? 'toggled':'';
		this.buttonClass  = ( this.buttonClass == '' ) ? 'active':'';
	}
	
	toggleMenuHeader(event){
		if (!this.elem.nativeElement.contains(event.target)){
			this.toggledClass 		= '';
			this.buttonClass 		= '';
		}		
	}
	
	getDataFromMiniCart(data){
		this.cartId = data.id;
		this.confimMsg = data.message; //'Are you sure, you want to delete this product?';
	}
	  
	removeItemFromMiniCart(){
		this.confimMsg = 'Waiting...';
		if(this.userId > 0){
			let myCart:any = this.auth.getCart();
			this.store.removeCart(this.cartId).subscribe(
				res => {
					if( res.status ){
						this.auth.setCart(res.data.cart);
						this.hideDeleteMiniCartModal.nativeElement.click();
						this.confimMsg = '';
						for(let i=0; i < myCart.length; i++){
							if( myCart[i]['cart_id'] == this.cartId ){
								this.track.trackRemoveItemFromCart(myCart[i]);
								break;
							}
						}
						this.cartId = 0;						
					}else{
						this.confimMsg = res.message;
					}
				},
				(err: HttpErrorResponse) => {
					this.confimMsg = "Sorry, there are some app issue!";
				}
			);
		}else{
			this.router.navigate(['/customer/login']);
		}
	}
	
	historyBack(){
		this.loc.back();
		//console.log("back page");
	}

}
