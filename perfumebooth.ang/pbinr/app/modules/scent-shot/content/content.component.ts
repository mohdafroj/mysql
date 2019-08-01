import { Component, OnInit, HostListener, ElementRef, AfterViewInit, AfterContentInit, ViewChild } from '@angular/core';
import { Title, Meta, MetaDefinition, DomSanitizer } from '@angular/platform-browser';
import { trigger, state, style, transition, animate, keyframes, query, stagger } from '@angular/animations';
import { NguCarousel, NguCarouselConfig, NguCarouselStore } 	from '@ngu/carousel';
import { Myconfig } 											from './../../../_services/pb/myconfig';
import { SeoService } from './../../../_services/seo.service';

@Component({
	selector: 'app-content',
	templateUrl: './content.component.html',
	styleUrls: ['./content.component.css'],
  	animations:[
		trigger("sect1FadeInDown",[
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInDown','-webkit-animation-name':'fadeInDown' }))
		]),
		trigger("sect1FadeIn",[
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeIn','-webkit-animation-name':'fadeIn' }))
		]),
		trigger("sect1FadeInUp",[
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInUp','-webkit-animation-name':'fadeInUp' }))
		]),
		trigger("selfieFadeInUp",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInUp','-webkit-animation-name':'fadeInUp' }))
		]),
		trigger("selfieFadeInRight",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInRight','-webkit-animation-name':'fadeInRight' }))
		]),
		trigger("perfumeFadeInUp",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInUp','-webkit-animation-name':'fadeInUp' }))
		]),
		trigger("perfumeFadeInDown",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInDown','-webkit-animation-name':'fadeInDown' }))
		]),		
		trigger("deoFadeInUp",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInUp','-webkit-animation-name':'fadeInUp' }))
		]),
		trigger("deoFadeInRight",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInRight','-webkit-animation-name':'fadeInRight' }))
		]),
		trigger("bodyMistFadeInDown",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInDown','-webkit-animation-name':'fadeInDown' }))
		]),		
		trigger("bodyMistFadeInUp",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInUp','-webkit-animation-name':'fadeInUp' }))
		])
	]

})
export class ContentComponent implements OnInit {
	
	nguSlider5: NguCarouselConfig;
	nguSlider5Token:string;
	nguSliderDataSource5:any;
	
	nguSlider9: NguCarouselConfig;
	nguSlider9Token:string;
	nguSliderDataSource9:any;
	
	nguSlider10: NguCarouselConfig;
	nguSlider10Token:string;
	nguSliderDataSource10:any;
	//@ViewChild('carousel') carousel: NguCarousel;
	
	sect2ItemActive:number 	= 0;
	sect6ItemActive:number 	= 0;
	constructor(private config:Myconfig, private meta:Meta, private title: Title, private el:ElementRef, private seo: SeoService) {
		
	}

	ngOnInit() {
		let title = 'Buy Scent Shot : Get 7 International Perfume in One Box';
		let metaDescription = 'Buy one perfume scent shot and get 7 international brands perfumes with seven unique fragrance at low cost. Easy to carry your perfumes scent shot in your pocket.';
		this.title.setTitle(title);
		let keyword: MetaDefinition = { name: 'keywords', content: 'Scent Shot, Luxury Scent Shot for Men and Women, Internation Brands Scent Shot, Perfumes for men, Perfume for women, buy Perfume online, online Perfume'};
		this.meta.addTag(keyword);
		let description: MetaDefinition = { name: 'description', content: metaDescription};
		this.meta.addTag(description);
		this.seo.ogMetaTag(
			'Buy Scent Shot : Get 7 International Perfume in One Box',
			'Buy one perfume scent shot and get 7 international brands perfumes with seven unique fragrance at low cost. Easy to carry your perfumes scent shot in your pocket.',
			'https://www.perfumebooth.com/assets/images/home/scent-shot.png'
		);
		this.config.scrollToTop();
		this.nguSlider5 = {
			grid: {xs: 1, sm: 1, md: 1, lg: 1, all: 0},
			slide: 1,
			speed: 400,
			interval:{
				timing:3000,
				initialDelay:1000
			},
			point: {
				visible: true,
				hideOnSingleSlide:true
			},
			load: 1,
			loop: true,
			touch: true,
			easing: 'ease'
		};
		
		this.nguSlider9 = {
			grid: {xs: 1.1, sm: 3, md: 3, lg: 3, all: 0},
			slide: 1,
			speed: 400,
			point: {
				visible: false
			},
			load: 1,
			touch: true,
			easing: 'ease'
		};
		
		this.nguSlider10 = {
			grid: {xs: 4.2, sm: 6, md: 6, lg: 6, all: 0},
			slide: 1,
			speed: 400,
			point: {
				visible: false
			},
			load: 1,
			touch: true,
			easing: 'ease'
		};
		
		this.nguSliderDataSource5 = [
			{
				"alt":"Male",
				"image":{
					"mobile":"assets/images/scent_shot/men_banner_mobile.jpg",
					"desktop":"assets/images/scent_shot/men_banner.jpg"
				}
			},
			{
				"alt":"Female",
				"image":{
					"mobile":"assets/images/scent_shot/girl_banner_mobile.jpg",
					"desktop":"assets/images/scent_shot/girl_banner.jpg"
				}
			}
		];
		this.nguSliderDataSource9 = [
			{
				"title":"Beyond Pour Homme",
				"image":"assets/images/scent_shot/our_products/BEYOND-POUR-HOMME-ART.jpg",
				"alt":"BEYOND-POUR-HOMME-ART",
				"link":"/Baugsons-Beyond-Pour-Homme-Eau-De-Parfum-100ml"
			},
			{
				"title":"Daring Femme",
				"image":"assets/images/scent_shot/our_products/DARING-FEMME-ART-2.jpg",
				"alt":"DARING-FEMME-ART-2",
				"link":"/MPF-Daring-Eau-De-Parfum-for-Women-100ml"
			},
			{
				"title":"Desert Prince",
				"image":"assets/images/scent_shot/our_products/DESERT-PRINCE-ART.jpg",
				"alt":"DESERT-PRINCE-ART",
				"link":"/Baugsons-Desert-Prince-Eau-De-Parfum-for-Men-100ml"
			},
			{
				"title":"Drops Rose",
				"image":"assets/images/scent_shot/our_products/DROPS-ROSE-ART.jpg",
				"alt":"DROPS-ROSE-ART",
				"link":"/MPF-Drops-Rose-Eau-De-Parfum-for-Women-100ml"
			},
			{
				"title":"Fancy Mural",
				"image":"assets/images/scent_shot/our_products/FANCY-MURAL-ART.jpg",
				"alt":"FANCY-MURAL-ART",
				"link":"/Mural-De-Ruitz-Fancy-Mural-Pour-Femme-Perfume-100ml"
			},
			{
				"title":"Ferocious Pour Homme",
				"image":"assets/images/scent_shot/our_products/FEROCIOUS-POUR-HOMME-ART-2.jpg",
				"alt":"FEROCIOUS-POUR-HOMME-ART-2",
				"link":"/MPF-Ferocious-Eau-De-Parfum-for-Men-100ml"
			},
			{
				"title":"Mural Energetic",
				"image":"assets/images/scent_shot/our_products/MURAL-ENERGETIC-ART.jpg",
				"alt":"MURAL-ENERGETIC-ART",
				"link":"/Mural-De-Ruitz-Energetic-Eau-De-Parfum-for-Men-100ml"
			},
			{
				"title":"Mural Thats Life",
				"image":"assets/images/scent_shot/our_products/MURAL-THATS-LIFE-ART.jpg",
				"alt":"MURAL-THATS-LIFE-ART",
				"link":"/Mural-De-Ruitz-Thats-Life-Eau-De-Parfum-for-Women-100ml"
			},
			{
				"title":"Treasures Night",
				"image":"assets/images/scent_shot/our_products/TREASURES-NIGHT-ART.jpg",
				"alt":"TREASURES-NIGHT-ART",
				"link":"/Baug-sons-Treasures-Night-Eau-De-Perfume-100ml"
			},
			{
				"title":"Virtual Pour Homme",
				"image":"assets/images/scent_shot/our_products/VIRTUAL-POUR-HOMME-ART.jpg",
				"alt":"VIRTUAL-POUR-HOMME-ART",
				"link":"/MPF-Virtuel-Pour-Homme-Eau-De-Parfum-100ml"
			}
		];
		this.nguSliderDataSource10 = [
			{
				"image":"assets/images/scent_shot/brand_logo/mpf.png",
				"alt":"MPF",
				"link":"/perfume-bottle/mpf-perfumes"
			},
			{
				"image":"assets/images/scent_shot/brand_logo/mural.png",
				"alt":"Mural",
				"link":"/perfume-bottle/Mural-De-Ruitz"
			},
			{
				"image":"assets/images/scent_shot/brand_logo/baug-sons.png",
				"alt":"Baug Sons",
				"link":"/perfume-bottle/baugsons-perfumes"
			},
			{
				"image":"assets/images/scent_shot/brand_logo/chris-adams.png",
				"alt":"Chris Adams",
				"link":"/perfume-bottle/chris-adams-perfumes"
			},
			{
				"image":"assets/images/scent_shot/brand_logo/creation.png",
				"alt":"Creation",
				"link":"/perfume-bottle/creation-perfumes"
			},
			{
				"image":"assets/images/scent_shot/brand_logo/lomani.png",
				"alt":"Lomani",
				"link":"/perfume-bottle/lomani-perfumes"
			},
			{
				"image":"assets/images/scent_shot/brand_logo/louis-cardin.png",
				"alt":"Louis Cardin",
				"link":"/perfume-bottle/louis-cardin-perfumes"
			}
		];
	}
  
	@HostListener('window:scroll', ['$event'])
    checkScroll() {
		const scrollPosition = window.pageYOffset
		const componentPosition = this.el.nativeElement.offsetTop;
		const sect1Position = this.el.nativeElement.querySelector('#sect1').offsetHeight;
		const sect2Position = this.el.nativeElement.querySelector('#sect2').offsetHeight;
		const sect3Position = this.el.nativeElement.querySelector('#sect3').offsetHeight;
		let visiblePosition = componentPosition + sect1Position + 30;
		
		if( scrollPosition > visiblePosition ){
			visiblePosition += sect2Position; 
		}
		
		if( scrollPosition > visiblePosition ){
			visiblePosition += sect3Position; 
		}
		//console.log(window.innerHeight);
		//console.log("scrollPosition: "+scrollPosition+", sect1Position: "+sect1Position+", sect2Position: "+sect2Position+", sect3Position: "+sect3Position+", visiblePosition: "+visiblePosition);

    }
	
	initDataSlider5Fn(key: NguCarouselStore){
		this.nguSlider5Token = key.token;
	}

	initDataSlider9Fn(key: NguCarouselStore){
		this.nguSlider9Token = key.token;
	}

	initDataSlider10Fn(key: NguCarouselStore){
		this.nguSlider10Token = key.token;
	}


	setSectTwoClass(n:number){
		this.sect2ItemActive = n;
	}
	
	setSectSixClass(n:number){
		this.sect6ItemActive = n;
	}
	

}
