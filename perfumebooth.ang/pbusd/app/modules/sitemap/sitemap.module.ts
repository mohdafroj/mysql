import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { SitemapComponent }			 		from './sitemap/sitemap.component';

import { SitemapRoutingModule } 			from './sitemap-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,SitemapRoutingModule],
  declarations:[
    SitemapComponent
  ],
  exports:[
    SitemapComponent
  ]
})
export class SitemapModule { }
