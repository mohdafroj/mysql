import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { CartComponent } from './cart/cart.component';
import { CheckoutComponent } from './checkout/checkout.component';
import { PlacedComponent } from './placed/placed.component';

import { StoreRoutingModule } from './store-routing.module';
import { FailureComponent } from './failure/failure.component';
import { SuccessComponent } from './success/success.component';
import { UnauthorizedComponent } from './unauthorized/unauthorized.component';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,StoreRoutingModule],
  declarations:[
    CartComponent,
    CheckoutComponent,
    PlacedComponent,
    FailureComponent,
    SuccessComponent,
    UnauthorizedComponent
  ],
  exports:[
    CartComponent,
    CheckoutComponent,
    PlacedComponent
  ]
})
export class StoreModule { }
