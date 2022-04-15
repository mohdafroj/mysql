import { Component, OnInit } from '@angular/core';
import { SeoService } from './../../_services/seo.service';

@Component({
  selector: 'app-unauthorized',
  templateUrl: './unauthorized.component.html',
  styleUrls: ['./../../../assets/css/checkout.css', './unauthorized.component.css']
})
export class UnauthorizedComponent implements OnInit {
    constructor ( private seo: SeoService ) { }
    ngOnInit () {
       this.seo.ogMetaTag( 'Unauthorized Page', 'Unauthorized page description' );
    }
}
