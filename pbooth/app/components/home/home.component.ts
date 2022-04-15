import { Component, OnInit, ElementRef, HostListener } from '@angular/core';
import { Title, Meta, MetaDefinition } 		from '@angular/platform-browser';
import { trigger,state,style } from '@angular/animations';
import { ActivatedRoute, Params } from '@angular/router';
import { HttpParams, HttpErrorResponse } from '@angular/common/http';
import { ProductsService } from './../../_services/pb/products.service';
import { Myconfig } 	from './../../_services/pb/myconfig';
import { SeoService } 	from './../../_services/seo.service';
import { DataService } from './../../_services/data.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
  animations:[
		trigger("scentShotFadeInLeft",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInLeft','-webkit-animation-name':'fadeInLeft' }))
		]),
		trigger("scentShotFadeInDown",[
			state('false', style({ 'opacity':'0' })),
			state('true',  style({  '-webkit-animation-duration': '2s','animation-duration': '2s','-webkit-animation-fill-mode': 'both','animation-fill-mode':'both','animation-name':'fadeInDown','-webkit-animation-name':'fadeInDown' }))
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
export class HomeComponent implements OnInit {

	scentShotFadeInLeft:boolean = true;
	scentShotFadeInDown:boolean = true;	
	selfieFadeInUp:boolean 		= false;
	selfieFadeInRight:boolean 	= false;
	perfumeFadeInUp:boolean 	= false;
	perfumeFadeInDown:boolean 	= false;	
	deoFadeInUp:boolean 		= false;
	deoFadeInRight:boolean 		= false;
	bodyMistFadeInUp:boolean 	= false;
	bodyMistFadeInDown:boolean 	= false;
	
	homeContent:boolean			= true;
	saleBanner = 0;
	perfumersclubLink = ['/','perfume-bottle','Perfumers-Club'];
	scentshotLink = ['/','scent-shot'];
	lightRLink = ['/','lightr','products'];
	perfumesLink = ['/','perfume-bottle','all'];
	deodorantLink = ['/','deodorant'];
	bodymistLink = ['/','body-mist'];
	constructor(
		private productService: ProductsService,
		private seo:SeoService,
		private config: Myconfig,
		private meta: Meta,
		private title: Title,
		private el:ElementRef,
		private route: ActivatedRoute,
		private data: DataService
	) {
  		//console.log("home constructor called");
    }
    ngOnInit() {
	  let title = 'Buy Online Perfume Fragrance | Perfume Scent Shot For Men and Women';
	  let metaDescription = 'Get 7 Perfumes in a Scent Shot of top International Brand like Lomani, Emper, Baug Sons, Chris Adams, Maryaj, Mural De Ruitz, MPF, New NB, Perfumers Choice and Louis Cardin for Men and Women and individual as well.';
	  this.config.scrollToTop();
      this.title.setTitle(title);
      let keyword: MetaDefinition = { name: 'keywords', content: 'perfumes, fragrances, international perfume, men perfumes, girls perfume, ladies perfume'};
      this.meta.addTag(keyword,true);
      let description: MetaDefinition = { name: 'description', content: metaDescription};
      this.meta.addTag(description,true);
	  this.seo.ogMetaTag(
		'Buy Online Perfume Fragrance | Perfume Scent Shot For Men and Women',
		'Get 7 Perfumes in a Scent Shot of top International Brand like Lomani, Emper, Baug Sons, Chris Adams, Maryaj, Mural De Ruitz, MPF, New NB, Perfumers Choice and Louis Cardin for Men and Women and individual as well.',
		'https://www.perfumebooth.com/assets/images/home/perfume.png'
		);
	  //console.log(window.location.origin.includes('perfumebooth.com'));
      this.route.queryParams.subscribe((params: Params) => {
        if(params.keyword){ this.homeContent = false; }
      });
	  this.data.currentApprovalStripData.subscribe(obj => this.saleBanner = obj.homeBanner );
    }
		
	@HostListener('window:scroll', ['$event'])
    checkScroll() {
      const componentPosition = this.el.nativeElement.offsetTop
      const scrollPosition = window.pageYOffset
      if( scrollPosition > 200 ) { this.selfieFadeInUp = true;  this.selfieFadeInRight = true; }
      if( scrollPosition > 600 ) { this.perfumeFadeInUp = true; this.perfumeFadeInDown = true; }
      if( scrollPosition > 1100 ) { this.deoFadeInUp = true; 	this.deoFadeInRight = true; }
      if( scrollPosition > 1500 ) { this.bodyMistFadeInUp = true; this.bodyMistFadeInDown = true; }

      //console.log("componentPosition: "+componentPosition+", scrollPosition: "+scrollPosition);

    }

}
