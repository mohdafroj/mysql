import { Component, OnInit } from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-testimonials',
  templateUrl: './testimonials.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css', './testimonials.component.css']
})
export class TestimonialsComponent implements OnInit {

  constructor(private config: Myconfig, private seo: SeoService) { }

  ngOnInit() {
    this.config.scrollToTop();
    this.seo.ogMetaTag('Testimonials ', 'Testimonials description');
  }

}
