import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { TestimonialsComponent }			 from './testimonials/testimonials.component';

import { TestimonialsRoutingModule } 			from './testimonials-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,TestimonialsRoutingModule],
  declarations:[
    TestimonialsComponent
  ],
  exports:[
    TestimonialsComponent
  ]
})
export class TestimonialsModule { }
