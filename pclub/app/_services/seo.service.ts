import { Injectable, Inject } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { DOCUMENT } from '@angular/common';

@Injectable({
  providedIn: 'root'
})
export class SeoService {
	
    constructor(private title: Title, @Inject(DOCUMENT) private doc) {
   
    }
    setPageTitle(title: string) {
        this.title.setTitle(title);
    }   
    
	getPageTitle() {
        return this.title.getTitle();
    }
   createLinkForCanonicalURL() {
	  let currentLink = window.location.origin+window.location.pathname; 
      //let link: HTMLLinkElement = this.doc.createElement('link');
      //link.setAttribute('rel', 'canonical');
      //this.doc.head.appendChild(link);
      let link = this.doc.querySelector('link[rel="canonical"]');
      link.setAttribute('href', currentLink);
   }
   
	//create json-ld script tag for bussiness
	
	createJsonLd (rawData, objectType: string = "Website") {
		let jsonData = {
			"@context": "http://schema.org",
			"@type": objectType,
			"name":"Perfumers Club",
			"logo":"https://www.perfumersclub.com/assets/images/logo.svg",
			"image":"https://www.perfumersclub.com/assets/images/logo.svg",
			"hasMap": "",
			"url":  "https://www.perfumersclub.com/",
			"contactPoint":[
				{
					"@type": "ContactPoint",
					"telephone": "+91 9811830806",
					"contactType": "customer care service"
				}
			],
			"address": {       
				"@type": "PostalAddress",
				"image":"https://www.perfumersclub.com/assets/images/logo.svg",
				"email": "mailto:connect@perfumersclub.com",
				"addressLocality": "India",
				"addressRegion": "Delhi",
				"postalCode":"110015",
				"streetAddress": "70B/35A, 4th Floor, Rama Road, Industrial Area"
			},
			"sameAs": [
				"https://www.facebook.com/perfumersclub",
				"https://twitter.com/perfumersclub",
				"https://www.instagram.com/perfumers_club",
				"https://www.youtube.com/channel/UCLh3vMMFgD_iy3Q5tI5kKeg",
				"https://www.linkedin.com/company/perfumersclub"
			],
			"openingHours": "Mo,Tu,We,Th,Fr 09:30-06:30",
			"geo": {
				"@type": "GeoCoordinates",
				"latitude": "28.6595591",
				"longitude": "77.1457528"
			},
			"potentialAction": {
				"@type": "SearchAction",
				"target": "https://www.perfumersclub.com/search?q={search_term_string}",
				"query-input": "required name=search_term_string"
		   }
		};
		
		if (rawData) {
		  jsonData = Object.assign({}, jsonData, rawData);
		}
		let script = this.doc.querySelector('script[type="application/ld+json"]');
		script.text = `${JSON.stringify(jsonData)}`;
		return false;
	}
	
  ogMetaTag(ogTitle='', ogDescription='', ogImage='') {
	  let ogUrl = window.location.origin+window.location.pathname;
      let ogUrlObj = this.doc.querySelector('meta[property="og:url"]');
      if ( ogUrlObj != null ) {
		ogUrlObj.setAttribute("content", ogUrl);
	  }
	  
      let ogTitleObj = this.doc.querySelector('meta[property="og:title"]');
	  if ( ogTitleObj != null ) {
		ogTitleObj.setAttribute('content', ogTitle);
	  }
	  
      let ogImageObj = this.doc.querySelector('meta[property="og:image"]');
	  if ( ogImageObj != null ) {
		ogImageObj.setAttribute('content', ogImage);
	  }
	  
      let ogDescriptionObj = this.doc.querySelector('meta[property="og:description"]');
	  if ( ogDescriptionObj != null ) {
		ogDescriptionObj.setAttribute('content', ogDescription);
	  }
   }
   
   createAMPPageLink() {
	  let canonicalElement = this.doc.querySelector('link[rel="canonical"]');
	  let currentLink:string = window.location.origin+"/amp"+window.location.pathname;
      let amphtmlElement: HTMLLinkElement = this.doc.createElement('link');
      amphtmlElement.setAttribute('rel', 'amphtml');
      amphtmlElement.setAttribute('href', currentLink);
	  this.doc.head.insertBefore(amphtmlElement, canonicalElement);
   }
   
   removeAMPPageLink() {   
	  let amphtmlElement = this.doc.querySelector('link[rel="amphtml"]');
	  if ( amphtmlElement ) {
		this.doc.head.removeChild(amphtmlElement);
	  }
   }


}
