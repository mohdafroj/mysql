import { Component, OnInit } from '@angular/core';
@Component({
  selector: 'app-left-menu',
  templateUrl: './left-menu.component.html',
  styleUrls: [
		'./../../../assets/css/user-dashboard.css',
		'./left-menu.component.css'
	]
})
export class LeftMenuComponent implements OnInit {
	openClass:string = '';
	toggleOpenClass:string = '';
	constructor() { }
	ngOnInit() {
		window.scrollTo(0, 0);
	}
	
	toggleAction(){
		this.openClass = (this.openClass != '') ? '':'open';
		this.toggleOpenClass = (this.toggleOpenClass != '') ? '':'toggle_open';
	}

}
