import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HomeComponent } from './components/home/home.component';
import { DetailsComponent }							 from './components/products/details/details.component';
import { BrandsComponent } 						 	 from './components/brand/brands.component';
import { BrandPerfumesComponent } 			    	 from './components/brand/perfume/perfume.component';

const routes: Routes = [

	{path:'perfume-100ml', 			redirectTo  : 'perfume-bottle'},
	{path:'about-us', 				loadChildren: './modules/about-us/about-us.module#AboutUsModule'},
	{path:'privacy-policy', 		loadChildren: './modules/privacy-policy/privacy-policy.module#PrivacyPolicyModule'},
	{path:'know-more', 				loadChildren: './modules/know-more/know-more.module#KnowMoreModule'},
	{path:'terms-of-use', 			loadChildren: './modules/terms-conditions/terms-conditions.module#TermsConditionsModule'},
	{path:'contact-us', 			loadChildren: './modules/contact/contact.module#ContactModule'},
	{path:'testimonials', 			loadChildren: './modules/testimonials/testimonials.module#TestimonialsModule'},
	{path:'disclaimers', 			loadChildren: './modules/disclaimers/disclaimers.module#DisclaimersModule'},
	{path:'faq', 					loadChildren: './modules/faq/faq.module#FaqModule'},
	{path:'sitemap', 				loadChildren: './modules/sitemap/sitemap.module#SitemapModule'},
	{path:'prive-member', 			loadChildren: './modules/why-register/why-register.module#WhyRegisterModule'},
	{path:'scent-shot', 			loadChildren: './modules/scent-shot/scent-shot.module#ScentShotModule'},
	{path:'product-categories', 	loadChildren: './modules/category/category.module#CategoryModule'},
	{path:'customer', 				loadChildren: './customers/customers.module#CustomersModule'},
	{path:'store', 					loadChildren: './store/store.module#StoreModule'},
	{path:'checkout', 				loadChildren: './store/store.module#StoreModule'},
	
	{path:'', 						component: HomeComponent },
	{path:'perfume-bottle', 		component: BrandsComponent, 					data:{id:5, title:'Perfume Bottle'}},
	{path:':key/:key', 				component: BrandPerfumesComponent,				data:{id:0, title:'Loading...'}},
	{path:':key', 					component: DetailsComponent, 					data:{id:0, title:'Loading...'}},
	{path: '**', 					redirectTo: '/'}

];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
