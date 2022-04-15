import { Component, OnInit } from '@angular/core';
import { Myconfig } 		 from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-faq',
  templateUrl: './faq.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./faq.component.css']
})
export class FaqComponent implements OnInit {
	oneAtATime: boolean = true;
	index:number = 0;
	oldIndex:number = 0;
	
	constructor(private config: Myconfig, private seo: SeoService) {
		//aconfig.closeOthers = true;
		//aconfig.type = 'info';
	}

	ngOnInit() {
		this.config.scrollToTop();
		this.seo.ogMetaTag("FAQ Page", "FAQ description");
	}
  
	setAction(index:number){
		if( index != this.oldIndex ){
			this.index = index;
			this.oldIndex = index;
		}else{
			this.index = 0;
			this.oldIndex = 0;
		}
	}
}
