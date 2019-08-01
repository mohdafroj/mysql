import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatDialogModule} from "@angular/material";
import { MatFormFieldModule} 					from '@angular/material/form-field';
import { MatInputModule} 						from '@angular/material/input';

import { LoginComponent } from './login/login.component';
import { RegisterComponent } from './register/register.component';
import { ForgotComponent } from './forgot/forgot.component';
import { LeftMenuComponent } from './left-menu/left-menu.component';
import { ProfileComponent } from './profile/profile.component';
import { PictureComponent } from './picture/picture.component';
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

import { CustomersRoutingModule } from './customers-routing.module';
import { ShareEarnComponent } from './share-earn/share-earn.component';
import { ReferralNetworkComponent } from './referral-network/referral-network.component';
import { DeleteDialogComponent } from './address-book/delete-dialog/delete-dialog.component';

@NgModule({
  imports:[
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
	MatDialogModule,
	MatFormFieldModule,
	MatInputModule,
    CustomersRoutingModule
  ],
  declarations:[
    LoginComponent,
    RegisterComponent,
    ForgotComponent,
    LeftMenuComponent,
    ProfileComponent,
    PictureComponent,
    AccountDetailComponent,
    AddressBookComponent,
    ListComponent,
    InvoiceComponent,
    DetailsComponent,
    WishlistComponent,
    WalletComponent,
    ReviewsComponent,
    NewsletterComponent,
    SecurityComponent,
    ShareEarnComponent,
    ReferralNetworkComponent,
    DeleteDialogComponent
  ],
  exports:[
    LoginComponent,
    RegisterComponent,
    ForgotComponent,
    LeftMenuComponent,
    ProfileComponent,
    PictureComponent,
    AccountDetailComponent,
    AddressBookComponent,
    ListComponent,
    InvoiceComponent,
    DetailsComponent,
    WishlistComponent,
    WalletComponent,
    ReviewsComponent,
    NewsletterComponent,
    SecurityComponent
  ],
  entryComponents: [DeleteDialogComponent]
})
export class CustomersModule { }
