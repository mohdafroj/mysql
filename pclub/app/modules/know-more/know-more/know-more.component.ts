import { Component, OnInit } from '@angular/core';
import { Myconfig } 											from './../../../_services/pb/myconfig';

@Component({
  selector: 'app-know-more',
  templateUrl: './know-more.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./know-more.component.css']
})
export class KnowMoreComponent implements OnInit {

  constructor(private config:Myconfig) { }

  ngOnInit() {
	this.config.scrollToTop();
  }

}
