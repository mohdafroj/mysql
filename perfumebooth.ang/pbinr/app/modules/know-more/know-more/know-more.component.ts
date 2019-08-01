import { Component, OnInit } from '@angular/core';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-know-more',
  templateUrl: './know-more.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./know-more.component.css']
})
export class KnowMoreComponent implements OnInit {

  constructor(private config:Myconfig, private seo: SeoService) { }

  ngOnInit() {
	this.config.scrollToTop();
	this.seo.ogMetaTag("Know More Page", "Know More description");
  }

}
