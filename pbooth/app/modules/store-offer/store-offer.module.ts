import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { StoreOfferRoutingModule } from './store-offer-routing.module';
import { ProductModule } from './../../featured/product/product.module';
import { SaleComponent } from './sale/sale.component';
import { ClearanceComponent } from './clearance/clearance.component';


@NgModule({
  declarations: [
    SaleComponent,
    ClearanceComponent
  ],
  imports: [
    CommonModule,
    ProductModule,
    StoreOfferRoutingModule
  ]
})
export class StoreOfferModule { }
