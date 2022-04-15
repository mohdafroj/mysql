import { Component, OnInit } from '@angular/core';
import { Myconfig }   from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-about-us',
  templateUrl: './about-us.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./about-us.component.css']
})
export class AboutUsComponent implements OnInit {

	constructor(private config: Myconfig, private seo:SeoService) {
    }
	ngOnInit(){
		this.config.scrollToTop();		
		this.seo.ogMetaTag(
			'A Little Introducton About Perfumebooth',
			'Coming from a family that has been in the perfume business for the last 30 years, Rohit Agrawal has managed to understand the fragrance market perfectly. He has been born and brought up in an environment that speaks the ‘fragrance language’.',
			'https://www.perfumebooth.com/assets/images/logo.svg'
		);
	}

}
