import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HomeComponent } from './components/home/home.component';
import { CmsComponent } from './components/cms/cms.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { LoginGuard } from './_services/guards/login-guard.service';

const routes: Routes = [

	{ path: 'buynow', 				redirectTo: '/shopnow'},
	{ path: 'launch-offer', 		redirectTo: '/shopnow'},
	{ path: 'shopnow', 				loadChildren: () => import('./modules/shopnow/shopnow.module').then(m => m.ShopnowModule)},
	{ path: 'about-us', 			loadChildren: () => import('./modules/about-us/about-us.module').then(m => m.AboutUsModule)},
	{ path: 'scent-match', 			loadChildren: () => import('./modules/scent-match/scent-match.module').then(m => m.ScentMatchModule)},
	{ path: 'launch-offer', 		loadChildren: () => import('./modules/launch-offer/launch-offer.module').then(m => m.LaunchOfferModule)},
	{ path: 'winter-sale-offer', 	loadChildren: () => import('./modules/launch-offer/launch-offer.module').then(m => m.LaunchOfferModule)},
	{ path: 'buynow', 				loadChildren: () => import('./modules/buynow/buynow.module').then(m => m.BuynowModule)},
	{ path: 'privacy-policy', 		loadChildren: () => import('./modules/privacy-policy/privacy-policy.module').then(m => m.PrivacyPolicyModule)},
	{ path: 'know-more', 			loadChildren: () => import('./modules/know-more/know-more.module').then(m => m.KnowMoreModule)},
	{ path: 'terms-of-use', 		loadChildren: () => import('./modules/terms-conditions/terms-conditions.module').then(m => m.TermsConditionsModule)},
	{ path: 'contact-us', 			loadChildren: () => import('./modules/contact/contact.module').then(m => m.ContactModule)},
	{ path: 'testimonials', 		loadChildren: () => import('./modules/testimonials/testimonials.module').then(m => m.TestimonialsModule)},
	{ path: 'disclaimers', 			loadChildren: () => import('./modules/disclaimers/disclaimers.module').then(m => m.DisclaimersModule)},
	{ path: 'sitemap-test', 		loadChildren: () => import('./modules/sitemap/sitemap.module').then(m => m.SitemapModule)},
	{ path: 'faq', 					loadChildren: () => import('./modules/faq/faq.module').then(m => m.FaqModule)},
	{ path: 'why-register', 		loadChildren: () => import('./modules/why-register/why-register.module').then(m => m.WhyRegisterModule)},
	{ path: 'product-categories', 	loadChildren: () => import('./modules/category/category.module').then(m => m.CategoryModule)},
	{ path: 'customer', 			loadChildren: () => import('./customers/customers.module').then(m => m.CustomersModule)},
	{ path: 'store', 				loadChildren: () => import('./store/store.module').then(m => m.StoreModule)},
	{ path: 'checkout', 			loadChildren: () => import('./store/store.module').then(m => m.StoreModule)},
	
	{ path: '', 					component: HomeComponent , data: {id: 0, title: 'Perfumer’s Club : Top Indian Perfume | Premium Scents'} },
	{ path: 'login', 		        component: LoginComponent, canActivate: [ LoginGuard ], data: {id: 0, title: 'Login at PerfumersClub'}},
	{ path: 'registration', 		component: RegisterComponent, canActivate: [ LoginGuard ], data: {id: 0, title: 'Create account at PerfumersClub'}},
	//{ path: 'cms/:key', 			component: CmsComponent, data: {id:0, title:'Loading...'}},
	{ path: ':key', 				component: CmsComponent, data: {id:0, title:'Loading...'}},
	{ path: '**', 					redirectTo: '/'}

];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
