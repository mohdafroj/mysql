import { Component, OnInit } from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';

@Component({
  selector: 'pc-terms-and-conditions',
  templateUrl: './terms-and-conditions.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./terms-and-conditions.component.css']
})
export class TermsAndConditionsComponent implements OnInit {

  constructor(private config:Myconfig) { }

  ngOnInit() {
	this.config.scrollToTop();
  }

}
