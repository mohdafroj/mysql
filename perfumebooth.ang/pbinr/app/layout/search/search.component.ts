import { Component, OnInit } from '@angular/core';
import { FormControl} 				from '@angular/forms';
import { Router } 					from '@angular/router';
import { Observable} 				from 'rxjs';
import { map, startWith} 			from 'rxjs/operators';
//import { from }						from 'rxjs/observable/from';
import { ProductsService } 			from './../../_services/pb/products.service';

@Component({
  selector: 'product-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.css']
})
export class SearchComponent implements OnInit {

	productList:any				= [];
	myControl = new FormControl();
    filteredOptions: Observable<string[]>;
  
	constructor(private router: Router, private product: ProductsService) {
		this.product.getSuggestion('').subscribe(
			(res) => {
				this.productList = res;
			},(error) =>{
				console.log('There are some app issue');
			}
		);
	}
  
	ngOnInit(){
		this.filteredOptions = this.myControl.valueChanges
			.pipe(
				startWith(''), map(value => this._filter(value))
		    );
	}
	
	private _filter(value: string): string[] {
		const filterValue = value.toLowerCase();
		return this.productList.filter(item => item.title.toLowerCase().includes(filterValue));
	}
	
	//old search function
	storeSearch(search){
		if( search.searchkeyword != "" ){
			this.router.navigate(['/search'], { queryParams: search });
		}
		return false;
	}
	
	onSelection(item){
		if( item != "" ){
			this.router.navigate(['/'+item], { queryParams: {} });
		}
		return false;
	}
	
	submitKeyword(cont){
		var check:number = 1;
		
		this.productList.map( item => {
			if( item.title.toLowerCase == cont.value.toLowerCase ){
				check = 0;
				//this.router.navigate(['/'+item.title.urlKey], { queryParams: {} });
				console.log(1);
			}
		});
		if(check){
			this.router.navigate(['/search'], { queryParams: {'keyword':cont.value} });
				console.log(2);
		}
	}

}
