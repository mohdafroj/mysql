import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { Router,ActivatedRoute,NavigationEnd } 	from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { StoreService } from './../../../_services/pb/store.service';
import { CustomerService } from './../../../_services/pb/customer.service';
import { DataService } from './../../../_services/data.service';
import { TrackingService } from './../../../_services/tracking.service';
import { ToastrService } from 'ngx-toastr';
import { Subscription } from 'rxjs';

@Component({
  selector: 'delete-item',
  templateUrl: './delete.component.html',
  styleUrls: ['./delete.component.css']
})
export class DeleteComponent implements OnInit {
	@ViewChild('hideDeleteMiniCartModal') hideDeleteMiniCartModal: ElementRef;
    cartId = 0;
	userId = 0;
  confimMsg = '';
  subscription: Subscription;
   constructor(
    private router : Router,
	private toastr:ToastrService, 
	private route: ActivatedRoute,
	private customerService: CustomerService,
	private storeService: StoreService,
	private dataService: DataService,
	private trackingService: TrackingService
   ) { }

    ngOnInit() {
		this.userId = this.customerService.getId();
		this.subscription = this.dataService.getDeleteItem().subscribe(
			res => {
				if ( res ) {
					this.userId = res.userId;
					this.cartId = res.cartId;
					this.confimMsg = 'Sure, you want to remove item from cart!';
				}	
		});
    }

    removeItem(){
		this.confimMsg = 'Waiting...';
		if(this.userId > 0){
			let myCart: any = this.customerService.getCart();
			this.storeService.removeCart(this.cartId).subscribe(
				res => {
					if( res.status ){
						this.customerService.setCart(res.data.cart);
						this.hideDeleteMiniCartModal.nativeElement.click();
						this.confimMsg = '';
						for(let i=0; i < myCart.length; i++){
							if( myCart[i]['cart_id'] == this.cartId ){
								this.trackingService.trackRemoveItemFromCart(myCart[i]);
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
	
	ngOnDestroy() {
        this.subscription.unsubscribe();
    }
}
