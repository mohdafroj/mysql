import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'pc-wishlist',
  templateUrl: './wishlist.component.html',
  styleUrls: [
		'./wishlist.component.css'
	],
  encapsulation: ViewEncapsulation.None
})
export class WishlistComponent implements OnInit {
  msg:string;
  productId = 0;
  productIndex = -1;
  wishlist = [];
  constructor(
	private titleService: Title, 
	private route: ActivatedRoute, 
	private config:Myconfig,
	private toastr: ToastrService,
	private customer: CustomerService) {
    route.data.subscribe(res =>{
      titleService.setTitle(res.title);
    });
  }

  ngOnInit() {
   this.customer.getWishlist().subscribe(
      res => {
        this.wishlist = res.data;
      },
      (err: HttpErrorResponse) => {
          this.toastr.error('Sorry, try later!');
      }
    );

  }
  updateWishlist(){
    let fd = {itemId: this.productId};
    this.customer.updateWishlist(fd).subscribe(
      res => {
        if(res.status){
          for(let i=0; i < this.wishlist.length; i++ ){
            if( i == this.productIndex ){
              this.wishlist.splice(i,1); break;
			  this.productId = 0;
			  this.productIndex = -1;
            }
          }
        }else{
          this.toastr.error(res.message);
        }
      },
      (err: HttpErrorResponse) => {
          this.toastr.error('Sorry, try later!');
      }
    );
  }
  
	addCart( item ) {
		let product = {
			id: item.product.id,
			name: item.title,
			title: item.title,
			urlKey: item.product.url_key,
			quantity: 1,
			images: [
				{large : item.product.product_images.img_base}
			],
			price: item.price,
			discount: {},
			shortDescription: item.short_description,
		};
		switch ( this.customer.addIntoCart(product) ) {
			case 1 : 
				this.toastr.success('Item added into shopping cart successfully!');
				break;
			case 0 : 
				this.toastr.warning('Sorry, Item already added into shopping cart!');
				break;
			default : this.toastr.error('Sorry, There are some issue!');
		}			
	}

}
