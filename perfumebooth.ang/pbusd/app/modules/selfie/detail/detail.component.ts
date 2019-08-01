import { Component, OnInit, ViewChild } 									from '@angular/core';
import { Title, Meta, MetaDefinition,DomSanitizer } 			from '@angular/platform-browser';
import { HttpParams, HttpErrorResponse } 						from '@angular/common/http';
import { Router, ActivatedRoute, NavigationEnd, Params } 		from '@angular/router';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { NguCarousel, NguCarouselConfig, NguCarouselStore } 	from '@ngu/carousel';

@Component({
  selector: 'app-perfume-selfie-detail',
  templateUrl: './detail.component.html',
  styleUrls: [
	'./../../../../assets/css/static_page.css',
	'./detail.component.css'
	]
})
export class DetailComponent implements OnInit {
	fSlider:number = 0;
	
	nguFirst: NguCarouselConfig;
	nguSecond: NguCarouselConfig;
	
	nguFirstData: any;
	nguSecondData: any;
	
	myCarousel:any;
	nguFirstToken:any;
	
	@ViewChild('nguCarouselFirst') nguCarouselFirst: NguCarousel<any>;
	@ViewChild('nguCarouselSecond') nguCarouselSecond: NguCarousel<any>;	

    constructor (
		private meta:Meta, 
		private title: Title, 
		private router: Router, 
		private route: ActivatedRoute, 
		private config:Myconfig ) {
    }
    
	ngOnInit() {
		this.config.scrollToTop(0, 0);
		this.title.setTitle('Perfume Selfie : Buy International Brands Perfume Testers for Men and Women');
		let keyword: MetaDefinition = { name: 'keywords', content: 'International Brands Perfume Selfie, Online Perfume Tester, Original Perfume Testers Online, Perfume Testers'};
		this.meta.addTag(keyword);
		let description: MetaDefinition = { name: 'description', content: 'Buy Perfume Selfie and Get 7 international brands perfumes with 7 variants fragrance at lowest cost. Now carry your perfumes in your pocket.'};
		this.meta.addTag(description);
		
		this.nguFirst = {
			grid: {xs: 1, sm: 1, md: 1, lg: 1, all: 0},
			slide: 1,
			speed: 400,
			interval: {
				timing:2000,
				initialDelay:1000
			},
			point: {
				visible: false
			},
			load: 1,
			touch: true,
			easing: 'ease'
		};
		
		this.nguSecond = {
			grid: {xs: 1, sm: 1, md: 1, lg: 1, all: 0},
			slide: 1,
			speed: 2000,
			interval: {
				timing:2000,
				initialDelay:1000
			},
			point: {
				visible: false,
				hideOnSingleSlide:true
			},
			load: 1,
			touch: true,
			loop: true,
			easing: 'ease'
		};
		
		this.nguFirstData = [
			{
				"image":"assets/images/selfie_images/selfie_product/img_1.jpg"
			},
			{
				"image":"assets/images/selfie_images/selfie_product/img_2.jpg"
			},
			{
				"image":"assets/images/selfie_images/selfie_product/img_3.jpg"
			},
			{
				"image":"assets/images/selfie_images/selfie_product/img_4.jpg"
			},
			{
				"image":"assets/images/selfie_images/selfie_product/img_5.jpg"
			},
			{
				"image":"assets/images/selfie_images/selfie_product/img_6.jpg"
			}
		];
		this.nguSecondData = [
			{
				"title":"Chris Adams",
				"image":"assets/images/know_more/slider/chris-adams.jpg",
				"logo":"assets/images/brand_logo/chris-adams.jpg",
				"content":"Chris Adams, Paris is a house of perfumers skilled in the timeless art of perfume making, yet forever pushing boundaries, creating fragrances that are fresh and contemporary. This perfumery by the house of Nabeel, is dedicated to creating innovative fragrances and designs that are not only of a superior quality, but also enchant you with their unique signature scents."
			},
			{
				"title":"Creation",
				"image":"assets/images/know_more/slider/creation.jpg",
				"logo":"assets/images/brand_logo/creation.jpg",
				"content":"Creation is a new age brand from the house of My Perfumes Factory, UAE. A young brand that aims at creating unique and high quality fragrances."
			},
			{
				"title":"Emper",
				"image":"assets/images/know_more/slider/emper.jpg",
				"logo":"assets/images/brand_logo/emper.jpg",
				"content":"Emper perfumes and cosmetics is a young, dynmic company dedicated to creating an exquisite range of perfumes as well as a line of premium cosmetics."
			},
			{
				"title":"English Blazer",
				"image":"assets/images/know_more/slider/english-blazer.jpg",
				"logo":"assets/images/brand_logo/englis.jpg",
				"content":"English Blazer is the original world-class fragrance; accented with style and refinement, it is the expression of man's thirst for excellence and attention to detail."
			},
			{
				"title":"Lomani",
				"image":"assets/images/know_more/slider/lomani.jpg",
				"logo":"assets/images/brand_logo/lomani.jpg",
				"content":"Lomani, the star brand from the Parfums Parour, has been a leader for over 25 years in all international markets. With its flagship Eau de Toilette brand, LOMANI pour Homme, an original, direct and refined scent, it holds a special place in the world of perfume."
			},
			{
				"title":"Louis Cardin",
				"image":"assets/images/know_more/slider/louis-cardin.jpg",
				"logo":"assets/images/brand_logo/louis-cardin.jpg",
				"content":"Louis Cardin uses French Fine Fragrances in All its Perfume Variants which in turn gives Long lasting and excellent Diffusion. The R&D Department ensures to produce unique fragrances that has a soothing & Stimulating effect."
			},
			{
				"title":"Maryaj",
				"image":"assets/images/know_more/slider/maryaj.jpg",
				"logo":"assets/images/brand_logo/maryaj.jpg",
				"content":"Born out of the quest for simplicity and purity, Maryaj is the brainchild of AJMAL PERFUMES. A young brand that brings a fresh spin on 'lifestyle fragrances' reflecting the search for a new, contemporary freshness."
			}
		];
		
    }

	initDataFirstFn(data: NguCarouselStore){
		this.nguFirstToken = data;
		this.fSlider = data.currentSlide
	}
	
	setFirstSlide(i:number){
		this.fSlider = i;
		if( this.nguFirstToken ){
			this.nguFirstToken.moveTo(i);
		}else{
			//this.myFirstToken.moveTo(i);
		}
		console.log(this.myCarousel);
	}
		
}
