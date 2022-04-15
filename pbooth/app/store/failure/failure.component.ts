import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { CustomerService } from '../../_services/pb/customer.service';
import { HttpErrorResponse } from '@angular/common/http';
import { SeoService } from './../../_services/seo.service';

@Component({
  selector: 'app-failure',
  templateUrl: './failure.component.html',
  styleUrls: ['./../../../assets/css/checkout.css', './failure.component.css']
})
export class FailureComponent implements OnInit {
    orderNumber: any = '';
    constructor(private router: Router, private auth: CustomerService, private seo: SeoService) { }
    ngOnInit () {
        const successData: string = localStorage.getItem('successData');
        if ( successData != null ) {
            const successDataObj: any = JSON.parse(successData);
            this.orderNumber = successDataObj.orderNumber;
            // this.orderMessage = successDataObj.orderMessage;
            localStorage.removeItem('successData');
        }
        this.seo.ogMetaTag('Failure Page', 'Failure page description');
    }

    reOrders () {
        const formData: any = {
            orderNumber: this.orderNumber
        };
        this.auth.reOrder(formData).subscribe(
            res => {
                if (res.status) {
                    this.router.navigate(['/checkout/cart'], { queryParams: {} });
                } else {
                    alert(res.message);
                }
            },
            (err: HttpErrorResponse) => {
                console.log('Server Isse');
            }
        );
    }
}
