import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatDialogModule} from "@angular/material/dialog";
import { MatFormFieldModule} from '@angular/material/form-field';
import { MatInputModule} from '@angular/material/input';
import { ClipboardModule } from 'ngx-clipboard';

import { CustomerComponent } from './customer/customer.component';
import { ProfileComponent } from './profile/profile.component';
import { AccountDetailComponent } from './account-detail/account-detail.component';
import { AddressBookComponent } from './address-book/address-book.component';
import { ListComponent } from './orders/list/list.component';
import { InvoiceComponent } from './orders/invoice/invoice.component';
import { DetailsComponent } from './orders/details/details.component';
import { WalletComponent } from './wallet/wallet.component';
import { WishlistComponent } from './wishlist/wishlist.component';
import { NewsletterComponent } from './newsletter/newsletter.component';
import { ReviewsComponent } from './reviews/reviews.component';

import { CustomersRoutingModule } from './customers-routing.module';
import { ShareEarnComponent } from './share-earn/share-earn.component';
import { DeleteDialogComponent } from './address-book/delete-dialog/delete-dialog.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { RedeemComponent } from './redeem/redeem.component';

@NgModule({
  imports:[
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
	MatDialogModule,
	MatFormFieldModule,
	MatInputModule,
    CustomersRoutingModule,
	ClipboardModule
  ],
  declarations:[
    CustomerComponent,
    ProfileComponent,
    AccountDetailComponent,
    AddressBookComponent,
    ListComponent,
    InvoiceComponent,
    DetailsComponent,
    WalletComponent,
	WishlistComponent,
    NewsletterComponent,
    ShareEarnComponent,
	ReviewsComponent,
    DeleteDialogComponent,
    CustomerComponent,
    DashboardComponent,
    RedeemComponent
  ],
  exports:[
	CustomerComponent,
    ProfileComponent,
    AccountDetailComponent,
    AddressBookComponent,
    ListComponent,
    InvoiceComponent,
    DetailsComponent,
    WalletComponent,
	WishlistComponent,
	ReviewsComponent,
	ShareEarnComponent,
    NewsletterComponent
  ],
  entryComponents: [DeleteDialogComponent]
})
export class CustomersModule { }
