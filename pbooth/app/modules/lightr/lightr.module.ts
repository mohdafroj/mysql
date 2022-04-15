import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { NguCarouselModule } from '@ngu/carousel';
import { AccordionModule } from 'ngx-bootstrap/accordion';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { ProductModule } from '../../featured/product/product.module';

import { ProductsComponent } from './products/products.component';
import { LightrRoutingModule } from './lightr-routing.module';

@NgModule({
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
    NguCarouselModule,
    LightrRoutingModule,
	MatProgressBarModule,
	ProductModule,
    AccordionModule
  ],
  declarations: [
    ProductsComponent
  ],
  exports: [
    ProductsComponent
  ]
})
export class LightrModule { }
