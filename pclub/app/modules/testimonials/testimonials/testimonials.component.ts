import { Component, OnInit } from '@angular/core';
import { Myconfig } 											from './../../../_services/pb/myconfig';

@Component({
  selector: 'app-testimonials',
  templateUrl: './testimonials.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./testimonials.component.css']
})
export class TestimonialsComponent implements OnInit {

  constructor(private config:Myconfig) { }

  ngOnInit() {
	this.config.scrollToTop();
  }

}
