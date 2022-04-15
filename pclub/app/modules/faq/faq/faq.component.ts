import { Component, OnInit } from '@angular/core';
import { Myconfig } 		 from './../../../_services/pb/myconfig';

@Component({
  selector: 'app-faq',
  templateUrl: './faq.component.html',
  styleUrls: ['./../../../../assets/css/static_page.css','./faq.component.css']
})
export class FaqComponent implements OnInit {
	oneAtATime: boolean = true;
	index:number = 0;
	oldIndex:number = 0;
	
	constructor(private config: Myconfig) {
		//aconfig.closeOthers = true;
		//aconfig.type = 'info';
	}

	ngOnInit() {
		this.config.scrollToTop();
	}
  
	setAction(index:number){
		if( index != this.oldIndex ){
			this.index = index;
			this.oldIndex = index;
		}else{
			this.index = 0;
			this.oldIndex = 0;
		}
	}
}
