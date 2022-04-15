import { Component, OnInit } from '@angular/core';
import { Myconfig } 		 from './../../../_services/pb/myconfig';

@Component({
  selector: 'app-disclaimers',
  templateUrl: './disclaimers.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./disclaimers.component.css']
})
export class DisclaimersComponent implements OnInit {

  constructor(private config:Myconfig) { }

  ngOnInit() {
	this.config.scrollToTop();
  }

}
