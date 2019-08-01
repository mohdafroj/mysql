import { NgModule }	from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SitemapComponent } from './sitemap/sitemap.component';

const modRoutes: Routes = [
    {path: '', component: SitemapComponent, data: {id: 0, title: 'PerfumeBooth: Sitemap!'} }
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class SitemapRoutingModule { }
