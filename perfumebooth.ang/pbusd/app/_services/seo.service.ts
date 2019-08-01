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
}
