import { Component,OnInit } 			from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
    selector: 'app-category',
    templateUrl: './category.component.html',
    styleUrls: [
		'./category.component.css',
		'./../../../../assets/css/product_category.css'
	]
})
export class CategoryComponent implements OnInit {
	constructor(private config: Myconfig, private seo:SeoService) {
    }
	ngOnInit(){
		this.config.scrollToTop();
		this.seo.ogMetaTag(
		    'PerfumeBooth: Product Categpries!',
			'Available high end international brands perfume and its different category. You can choose yours in just a couple of clicks at budget friendly prices. 100% original perfumes from popular brands for men and women at India\'s leading online fragrance store.',
			'https://www.perfumebooth.com/assets/images/home/perfume.png'
		);
	}
}
