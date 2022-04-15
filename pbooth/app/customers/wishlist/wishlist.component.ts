import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { Myconfig } from './../../_services/pb/myconfig';
import { CustomerService } from '../../_services/pb/customer.service';
import { SeoService } 	   							from './../../_services/seo.service';

@Component({
  selector: 'app-wishlist',
  templateUrl: './wishlist.component.html',
  styleUrls: [
		'./../../../assets/css/responsive-table.css',
		'./../../../assets/css/user-dashboard.css',
		'./wishlist.component.css'
	]
})
export class WishlistComponent implements OnInit {
  msg:string;
  wishlist:any;
  constructor(private seo: SeoService, private titleService: Title, private route: ActivatedRoute, private config:Myconfig, private auth: CustomerService) {
    route.data.subscribe(res =>{
      titleService.setTitle(res.title);
    });
  }

  ngOnInit() {
	this.seo.ogMetaTag('Customer Wishlist Page', 'Customer Wishlist page description');
    let response:any = {
      status:false,
      message:'',
      data:[]
    }
    this.auth.getWishlist().subscribe(
      res => {
        response = res;
        this.wishlist = response.data;
        //console.log(response);
      },
      (err: HttpErrorResponse) => {
        console.log("Server Isse!");
      }
    );

  }

  updateWishlist(id, index){
    this.msg = 'Wait...';
    let fd:any;
    fd = {itemId:id};
    let res:any={message:'',data:[],status:false}
    this.auth.updateWishlist(fd).subscribe(
      res => {
        if(res.status){
          for(let i=0; i < this.wishlist.length; i++ ){
            if( i == index ){
              this.wishlist.splice(i,1); break;
            }
          }
        }else{
          alert(this.msg = res.message);
        }
      },
      (err: HttpErrorResponse) => {
        if(err.error instanceof Error){
          this.msg = 'Client error: '+err.error.message;
        }else{
          this.msg = 'Server error: '+JSON.stringify(err.error);
        }
      }
    );
  }

}
