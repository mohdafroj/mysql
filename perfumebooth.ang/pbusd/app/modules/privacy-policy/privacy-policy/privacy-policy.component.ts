import { Component, OnInit } from '@angular/core';
import { Myconfig } 											from './../../../_services/pb/myconfig';

@Component({
  selector: 'app-privacy-policy',
  templateUrl: './privacy-policy.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./privacy-policy.component.css']
})
export class PrivacyPolicyComponent implements OnInit {

  constructor(private config:Myconfig) { }

  ngOnInit() {
	this.config.scrollToTop();
  }

}
