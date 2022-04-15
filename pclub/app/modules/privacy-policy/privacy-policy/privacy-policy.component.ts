import { Component, OnInit } from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';
import { PagesService } from './../../../_services/pb/pages.service';

@Component({
  selector: 'app-privacy-policy',
  templateUrl: './privacy-policy.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./privacy-policy.component.css']
})
export class PrivacyPolicyComponent implements OnInit {
	company = {};
	constructor(private config: Myconfig, private pages: PagesService ) { }

	ngOnInit() {
		this.config.scrollToTop();
		let infoData = this.pages.getCompanyData();
		this.company = infoData['company'];
	}

}
