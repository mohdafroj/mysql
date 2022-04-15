import { Component, OnInit } from '@angular/core';
import { Myconfig } 											from './../../../_services/pb/myconfig';

@Component({
  selector: 'app-why-register',
  templateUrl: './why-register.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./why-register.component.css']
})
export class WhyRegisterComponent implements OnInit {

  constructor(private config:Myconfig) { }

  ngOnInit() {
	this.config.scrollToTop();
  }

}
