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
	  if ( link ) {
		link.setAttribute('href', currentLink);
	  }
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
