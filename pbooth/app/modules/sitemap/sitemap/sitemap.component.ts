import { Component, OnInit } from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';

@Component({
  selector: 'app-sitemap',
  templateUrl: './sitemap.component.html',
  styleUrls: ['./sitemap.component.css']
})
export class SitemapComponent implements OnInit {

  constructor(private config: Myconfig) { }

  ngOnInit() {
     this.config.scrollToTop();
  }

}
