import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HomeComponent } from './components/home/home.component';
import { DetailsComponent }							 from './components/products/details/details.component';
import { BrandsComponent } 						 	 from './components/brand/brands.component';
import { BrandPerfumesComponent } 			    	 from './components/brand/perfume/perfume.component';
import { SaleComponent } 			    	 		 from './components/products/sale/sale.component';
import { CmsComponent } 							 from './components/cms/cms.component';

const routes: Routes = [

	{path:'perfume-100ml', 			redirectTo  : 'perfume-bottle'},
	{path:'perfume-selfie-offer',	redirectTo  : 'perfume-selfie'},
	{path:'perfume-selfie-offer/products',redirectTo  : 'perfume-selfie/products'},
	{path:'perfumebooth.html', 		redirectTo  : 'perfume-selfie'},
	{path:'body-mist', 				redirectTo  : '/body-mist/jacqui-david-passion-bodymist'},
	{path:'about-us', 				loadChildren: './modules/about-us/about-us.module#AboutUsModule'},
	//{path:'privacy-policy', 		loadChildren: './modules/privacy-policy/privacy-policy.module#PrivacyPolicyModule'},
	{path:'know-more', 				loadChildren: './modules/know-more/know-more.module#KnowMoreModule'},
	//{path:'terms-of-use', 			loadChildren: './modules/terms-conditions/terms-conditions.module#TermsConditionsModule'},
	{path:'contact-us', 			loadChildren: './modules/contact/contact.module#ContactModule'},
	{path:'testimonials', 			loadChildren: './modules/testimonials/testimonials.module#TestimonialsModule'},
	//{path:'disclaimers', 			loadChildren: './modules/disclaimers/disclaimers.module#DisclaimersModule'},
	{path:'faq', 					loadChildren: './modules/faq/faq.module#FaqModule'},
	{path:'sitemap', 				loadChildren: './modules/sitemap/sitemap.module#SitemapModule'},
	{path:'prive-member', 			loadChildren: './modules/why-register/why-register.module#WhyRegisterModule'},
	{path:'scent-shot', 			loadChildren: './modules/scent-shot/scent-shot.module#ScentShotModule'},
	{path:'perfume-selfie', 		loadChildren: './modules/selfie/selfie.module#SelfieModule'},
	{path:'product-categories', 	loadChildren: './modules/category/category.module#CategoryModule'},
	{path:'customer', 				loadChildren: './customers/customers.module#CustomersModule'},
	{path:'store', 					loadChildren: './store/store.module#StoreModule'},
	{path:'checkout', 				loadChildren: './store/store.module#StoreModule'},
	
	//{ path:'testimonials', 			component: CmsComponent, data:{id:0, title:'PerfumeBooth: Testimonials!'} },
	//{ path:'about-us', 			    component: CmsComponent, data:{id:0, title:'PerfumeBooth: About Us!'} },
	{ path:'disclaimers', 			component: CmsComponent, data:{id:0, title:'PerfumeBooth: Disclaimers!'} },
	{ path:'privacy-policy', 		component: CmsComponent, data:{id:0, title:'PerfumeBooth: Privacy Policy!'} },
	{ path:'terms-of-use', 			component: CmsComponent, data:{id:0, title:'PerfumeBooth: Terms and Conditions!'} },
	{path:'', 						component: HomeComponent },
	{path:'search', 				component: HomeComponent},
	{path:'perfume-bottle', 		component: BrandsComponent, 					data:{id:5, title:'Perfume Bottle'}},
	{path:'deodorant', 				component: BrandsComponent, 					data:{id:9, title:'Deodorant'}},		
	{path:'store-offer', 		 	component: SaleComponent, 						data:{id:0, title:'Buy Online Perfume Fragrance | Perfume For Men and Women'}},
	{path:'cms/:key', 		 		component: CmsComponent, 						data:{id:0, title:'Buy Online Perfume Fragrance | Perfume For Men and Women'}},
	{path:':key/:key', 				component: BrandPerfumesComponent,				data:{id:0, title:'Loading...'}},
	{path:':key', 					component: DetailsComponent, 					data:{id:0, title:'Loading...'}},
	//{path:'offer/:key/:key', 		component: BrandPerfumesComponent,				data:{id:0, title:'Loading...'}},
	//{path:'offer/perfume-bottle', component: BrandsComponent, 					data:{id:5, title:'Perfume Bottle'}},
	//{path:'offer/deodorant', 		component: BrandsComponent, 					data:{id:9, title:'Deodorant'}},	
	{path: '**', 					redirectTo: '/'}

];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
