import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HomeComponent } from './components/home/home.component';
import { BrandsComponent } 						 	 from './components/brand/brands.component';
import { BrandPerfumesComponent } 			    	 from './components/brand/perfume/perfume.component';
import { CmsComponent } 							 from './components/cms/cms.component';
const routes: Routes = [
	{ path:'perfume-100ml', 		redirectTo  : 'perfume-bottle'},
	{ path:'perfume-selfie-offer',	redirectTo  : 'perfume-selfie'},
	{ path:'perfume-selfie-offer/products',redirectTo  : 'perfume-selfie/products'},
	{ path:'perfumebooth.html', 	redirectTo  : 'perfume-selfie'},
	{ path:'body-mist', 			redirectTo  : '/body-mist/jacqui-david-passion-bodymist'},
	{ path:'launch-offer', 			loadChildren: () => import('./modules/launch-offer/launch-offer.module').then(m => m.LaunchOfferModule)},
	{ path:'store-offer', 			loadChildren: () => import('./modules/store-offer/store-offer.module').then(m => m.StoreOfferModule)},
	{ path:'about-us', 				loadChildren: () => import('./modules/about-us/about-us.module').then(m => m.AboutUsModule)},
	{ path:'know-more', 			loadChildren: () => import('./modules/know-more/know-more.module').then(m => m.KnowMoreModule)},
	{ path:'contact-us', 			loadChildren: () => import('./modules/contact/contact.module').then(m => m.ContactModule)},
	{ path:'testimonials', 			loadChildren: () => import('./modules/testimonials/testimonials.module').then(m=>m.TestimonialsModule)},
	{ path:'disclaimers', 			loadChildren: () => import('./modules/disclaimers/disclaimers.module').then(m => m.DisclaimersModule)},
	{ path:'privacy-policy', 		loadChildren: () => import('./modules/privacy-policy/privacy-policy.module').then(m => m.PrivacyPolicyModule)},
	{ path:'terms-of-use', 		 	loadChildren: () => import('./modules/terms-conditions/terms-conditions.module').then(m => m.TermsConditionsModule)},
	{ path:'faq', 					loadChildren: () => import('./modules/faq/faq.module').then(m => m.FaqModule)},
	{ path:'sitemap', 				loadChildren: () => import('./modules/sitemap/sitemap.module').then(m => m.SitemapModule)},
	{ path:'prive-member', 			loadChildren: () => import('./modules/why-register/why-register.module').then(m => m.WhyRegisterModule)},
	{ path:'lightr', 				loadChildren: () => import('./modules/lightr/lightr.module').then(m => m.LightrModule)},
	{ path:'scent-shot', 			loadChildren: () => import('./modules/scent-shot/scent-shot.module').then(m => m.ScentShotModule)},
	{ path:'perfume-selfie', 		loadChildren: () => import('./modules/selfie/selfie.module').then(m => m.SelfieModule)},
	{ path:'product-categories', 	loadChildren: () => import('./modules/category/category.module').then(m => m.CategoryModule)},
	{ path:'customer', 				loadChildren: () => import('./customers/customers.module').then(m => m.CustomersModule)},
	{ path:'store', 				loadChildren: () => import('./store/store.module').then(m => m.StoreModule)},
	{ path:'checkout', 				loadChildren: () => import('./store/store.module').then(m => m.StoreModule)},	
	{ path:'', 						component: HomeComponent },
	{ path:'perfume-bottle', 		component: BrandsComponent, 					data:{id:5, title:'Perfume Bottle'}},
	{ path:'deodorant', 			component: BrandsComponent, 					data:{id:9, title:'Deodorant'}},		
	{ path:':key/:key', 			component: BrandPerfumesComponent,				data:{id:0, title:'Loading...'}},
	{ path:':key', 					component: CmsComponent, 					    data:{id:0, title:'Buy Online Perfume Fragrance | Perfume For Men and Women'}},
	{ path: '**', 					redirectTo: '/'}
];
@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
