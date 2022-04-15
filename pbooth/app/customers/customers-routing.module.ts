import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import { ProfileComponent } from './profile/profile.component';
import { AccountDetailComponent } from './account-detail/account-detail.component';
import { AddressBookComponent } from './address-book/address-book.component';
import { ListComponent } from './orders/list/list.component';
import { InvoiceComponent } from './orders/invoice/invoice.component';
import { DetailsComponent } from './orders/details/details.component';
import { WishlistComponent } from './wishlist/wishlist.component';
import { WalletComponent } from './wallet/wallet.component';
import { ReviewsComponent } from './reviews/reviews.component';
import { NewsletterComponent } from './newsletter/newsletter.component';
import { SecurityComponent } from './security/security.component';
import { LoginGuard } from './../_services/guards/login-guard.service';
import { AuthGuard } from '../_services/guards/auth-guard.service';

const customersRoutes: Routes = [
  {path:'register',         redirectTo  : 'registration'},
  {path:'signup',           redirectTo  : 'registration'},
  {path:'sign-up',          redirectTo  : 'registration'},
  {path:'signip',           redirectTo  : 'login'},
  {path:'sign-ip',          redirectTo  : 'login'},
  {path:'forgot',           redirectTo  : 'login'},
  {path:'',                 component: LoginComponent,          canActivate:[LoginGuard], data:{id:0,title:'Customer Login'}},
  {path:'login',            component: LoginComponent,          canActivate:[LoginGuard], data:{id:0,title:'Customer Login'}},
  {path:'registration',     component: RegisterComponent,       canActivate:[LoginGuard], data:{id:0,title:'Customer Register'}},

  {path:'profile',          component: ProfileComponent,        canActivate:[AuthGuard], data:{id:0,title:'Customer Profile'}},
  {path:'account-detail',   component: AccountDetailComponent,  canActivate:[AuthGuard], data:{id:0,title:'Edit Customer Profile'}},
  {path:'address-book',     component: AddressBookComponent,    canActivate:[AuthGuard], data:{id:0,title:'Customer Address Book'}},
 
  {path:'orders',           component: ListComponent,           canActivate:[AuthGuard], data:{id:0,title:'Customer Orders'}},
  {path:'orders/details',   component: DetailsComponent,        canActivate:[AuthGuard], data:{id:0,title:'Customer Order Details'}},
  {path:'orders/invoice',   component: InvoiceComponent,        canActivate:[AuthGuard], data:{id:0,title:'Customer Orders Invoice'}},
  {path:'wishlist',         component: WishlistComponent,       canActivate:[AuthGuard], data:{id:0,title:'Customer Wishlist'}},
  {path:'wallet',           component: WalletComponent,         canActivate:[AuthGuard], data:{id:0,title:'Customer Wallet'}},
  {path:'share-and-earn',   component: WalletComponent,         canActivate:[AuthGuard], data:{id:0,title:'Share Earn'}},
  {path:'referral-network', component: WalletComponent,         canActivate:[AuthGuard], data:{id:0,title:'Referral Network'}},
  {path:'products-reviews', component: ReviewsComponent,        canActivate:[AuthGuard], data:{id:0,title:'Customer Product Reviews'}},
  {path:'newsletter',       component: NewsletterComponent,     canActivate:[AuthGuard], data:{id:0,title:'Customer News Letter'}},
  {path:'security-updates', component: SecurityComponent,       canActivate:[AuthGuard], data:{id:0,title:'Customer Security Updates'}},
];

@NgModule({
  imports: [RouterModule.forChild(customersRoutes)],
  exports: [RouterModule]
})
export class CustomersRoutingModule { }
