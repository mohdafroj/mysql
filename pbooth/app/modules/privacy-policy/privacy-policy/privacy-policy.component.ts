import { Component, OnInit } from '@angular/core';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-privacy-policy',
  templateUrl: './privacy-policy.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./privacy-policy.component.css']
})
export class PrivacyPolicyComponent implements OnInit {

  constructor(private config:Myconfig, private seo: SeoService) { }

  ngOnInit() {
	this.config.scrollToTop();
	this.seo.ogMetaTag("Privacy Policy Page", "Privacy Policy description");
  }

}
