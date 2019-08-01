import { Component,OnInit } 	from '@angular/core';
import { Router, ActivatedRoute } 					from '@angular/router';
import { trigger,state,style,transition,animate,keyframes,query,stagger } 	from '@angular/animations';
import { Title, Meta, MetaDefinition } 						from '@angular/platform-browser';
import { HttpParams, HttpErrorResponse } 									from '@angular/common/http';
import { Myconfig } 														from './../../_services/pb/myconfig';
import { ProductsService } 													from './../../_services/pb/products.service';

@Component({
    selector: 'app-brand-perfume',
    templateUrl: './brands.component.html',
    styleUrls: [
		'./brands.component.css',
	],
	animations:[
		trigger("perfumeNewNBFadeInDown",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInDown','-webkit-animation-name':'fadeInDown' }))
		]),		
		trigger("perfumeNewNBFadeInUp",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInUp','-webkit-animation-name':'fadeInUp' }))
		])
	]
})
export class BrandsComponent implements OnInit {
	categoryId:number = 0;
	brands:any = {};
	resultStatus:number		= 0;
	resultMsg:string		= '';
	
	perfumeNewNBFadeInDown:boolean = true;
	perfumeNewNBFadeInUp:boolean = true;
	
	keyword:MetaDefinition;
	description:MetaDefinition;
	
	constructor(private meta:Meta, private title: Title, private route: ActivatedRoute, private products: ProductsService, private config: Myconfig) {
    }
	
	ngOnInit(){
		this.config.scrollToTop();
		this.route.data.subscribe(res =>{
            this.categoryId = res.id;
        });
		let prms = new HttpParams();
        prms = prms.append('category-id', `${this.categoryId}`);
		this.getCategoryBrands(prms);
		
		this.route.data.subscribe( res => {
			switch( res.id ){
				case 5: this.title.setTitle('Online Perfume Store : Perfume Bottle For Men and Women');
						this.keyword = {name: 'keywords', content: 'Perfume Bottle For Man, Perfume Bottle For Women, Online Perfume Store, Man Perfume Bottle, Best Perfume Bottles'};
						this.description = {name: 'description', content: 'Buy online original international brands perfumes big bottle at discount price in India. Buy and try Luxury perfumes today!'};
					break;
				case 9: this.title.setTitle('Deodorants : Buy Deodorants Online for Men and Women at best price');
						this.keyword = {name: 'keywords', content: 'Deodorant For Men, Deodorant for Women, Deodorants Online for Men, International Brands Deodorants'};
						this.description = {name: 'description', content: 'Perfumebooth Offers you to choose wide range of international brands deodorant and get discounted price.'};
					break;
				default	:
			}
			this.meta.addTag(this.keyword);
			this.meta.addTag(this.description);			
		});

	}
	
    getCategoryBrands(prms){
		this.resultStatus		= 0;
		this.products.getCategoryBrands(prms).subscribe(
            res => {
				if( res.status ){
					this.brands = res.data;
					this.resultMsg		= '';
				}else{
					this.resultMsg		= res.message;
				}
				this.resultStatus		= 1;
				//console.log(this.brands);
            },
            (err: HttpErrorResponse) => {
                if(err.error instanceof Error){
					this.resultMsg		= err.error.message;
                }else{
					this.resultMsg		= JSON.stringify(err.error);
                }
				this.resultStatus		= 1;
            }
        );
    }
	
	numToArray(num){
      return Array.from(Array(num).keys());
    }
	
}
