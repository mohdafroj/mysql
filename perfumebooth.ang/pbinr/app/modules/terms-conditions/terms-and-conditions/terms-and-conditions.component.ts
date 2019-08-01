import { Component, OnInit } from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-terms-and-conditions',
  templateUrl: './terms-and-conditions.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css', './terms-and-conditions.component.css']
})
export class TermsAndConditionsComponent implements OnInit {

  constructor(private config: Myconfig, private seo: SeoService) { }

  ngOnInit() {
    this.config.scrollToTop();
    this.seo.ogMetaTag('Terms and Conditions', 'Terms and conditions description');
  }

}
