import { Component,OnInit } 			from '@angular/core';
import { Myconfig } from './../../../_services/pb/myconfig';

@Component({
    selector: 'app-category',
    templateUrl: './category.component.html',
    styleUrls: [
		'./category.component.css',
		'./../../../../assets/css/product_category.css'
	]
})
export class CategoryComponent implements OnInit {
	constructor(private config: Myconfig) {
    }
	ngOnInit(){
		this.config.scrollToTop();
	}
}
