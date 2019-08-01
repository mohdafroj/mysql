import { Component, OnInit } from '@angular/core';
import { Myconfig } 		 from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-disclaimers',
  templateUrl: './disclaimers.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./disclaimers.component.css']
})
export class DisclaimersComponent implements OnInit {

  constructor(private config:Myconfig, private seo:SeoService ) { }

  ngOnInit() {
	this.config.scrollToTop();
	this.seo.ogMetaTag("Disclaimers", "Disclaimers description");
	
  }

}
