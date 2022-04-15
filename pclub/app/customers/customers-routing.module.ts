import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { CustomerComponent } from './customer/customer.component';
import { ProfileComponent } from './profile/profile.component';
import { AccountDetailComponent } from './account-detail/account-detail.component';
import { AddressBookComponent } from './address-book/address-book.component';
import { ListComponent } from './orders/list/list.component';
import { DetailsComponent } from './orders/details/details.component';
import { InvoiceComponent } from './orders/invoice/invoice.component';
import { WalletComponent } from './wallet/wallet.component';
import { WishlistComponent } from './wishlist/wishlist.component';
import { ReviewsComponent } from './reviews/reviews.component';
import { ShareEarnComponent } from './share-earn/share-earn.component';
import { RedeemComponent } from './redeem/redeem.component';
import { NewsletterComponent } from './newsletter/newsletter.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { AuthGuard } from '../_services/guards/auth-guard.service';

const customersRoutes: Routes = [
    { 
		path: '', component: CustomerComponent,
		canActivate: [ AuthGuard ],
		children: [
			{ path: '',          		component: DashboardComponent,      data: {id: 0, title: 'Customer Dashboard'} },
			{ path: 'profile',          component: ProfileComponent,        data: {id: 0, title: 'Customer Profile'} },
			{ path: 'account-detail',   component: AccountDetailComponent,  data: {id: 0, title: 'Edit Customer Profile'} },
			{ path: 'address-book',     component: AddressBookComponent,    data: {id: 0, title: 'Customer Address Book'} },
			{ path: 'orders',           component: ListComponent,           data: {id: 0, title: 'Customer Orders'} },
			{ path: 'orders/details',   component: DetailsComponent,        data: {id: 0, title: 'Customer Order Details'} },
			{ path: 'orders/invoice',   component: InvoiceComponent,        data: {id: 0, title: 'Customer Orders Invoice'} },
			{ path: 'wallet',           component: WalletComponent,         data: {id: 0, title: 'Customer Wallet'} },
			{ path: 'wishlist',         component: WishlistComponent,       data: {id: 0, title: 'Customer Wishlist'} },
			{ path: 'reviews',          component: ReviewsComponent,        data: {id: 0, title: 'Customer Reviews'} },
			{ path: 'refer-and-earn',   component: ShareEarnComponent,      data: {id: 0, title: 'Refer and Earn'} },
			{ path: 'refer-and-earn/redeem-now',   component: RedeemComponent,  data: {id: 0, title: 'Redeem - Refer and Earn'} },
			{ path: 'newsletter',       component: NewsletterComponent,     data: {id: 0, title: 'Customer News Letter'} }
		]
	}
];

@NgModule({
  imports: [RouterModule.forChild(customersRoutes)],
  exports: [RouterModule]
})
export class CustomersRoutingModule { }
