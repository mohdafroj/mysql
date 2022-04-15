import { Component, OnInit } from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
  selector: 'app-why-register',
  templateUrl: './why-register.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css', './why-register.component.css']
})
export class WhyRegisterComponent implements OnInit {

  constructor(private config: Myconfig, private seo: SeoService) { }

  ngOnInit() {
    this.config.scrollToTop();
    this.seo.ogMetaTag(
		'PerfumeBooth: Prive Member!',
		'Perfumebooth Privé membership gives you undeterred access to the world of perfume. Get special privileges, points and members’ only discounts by becoming a member.',
		'https://www.perfumebooth.com/assets/images/static_pics/prive_member.jpg'
	);
  }
}
