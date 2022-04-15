import { Component, OnInit } from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';

@Component({
  selector: 'app-about-us',
  templateUrl: './about-us.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./about-us.component.css']
})
export class AboutUsComponent implements OnInit {

	constructor(private config: Myconfig) {
    }
	ngOnInit(){
		this.config.scrollToTop();
	}

}
