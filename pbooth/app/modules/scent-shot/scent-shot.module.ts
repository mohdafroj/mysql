import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { NguCarouselModule } from '@ngu/carousel';
import { AccordionModule } from 'ngx-bootstrap/accordion';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { ProductModule } from './../../featured/product/product.module';

import { ContentComponent } from './content/content.component';
import { ProductsComponent } from './products/products.component';
import { RefillsComponent } from './refills/refills.component';
import { PerfumesComponent } from './perfumes/perfumes.component';
import { ScentShotRoutingModule } from './scent-shot-routing.module';

@NgModule({
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
    NguCarouselModule,
    ScentShotRoutingModule,
	MatProgressBarModule,
	ProductModule,
    AccordionModule
  ],
  declarations: [
    ContentComponent,
    ProductsComponent,
    RefillsComponent,
    PerfumesComponent
  ],
  exports: [
    ContentComponent,
    ProductsComponent
  ]
})
export class ScentShotModule { }
