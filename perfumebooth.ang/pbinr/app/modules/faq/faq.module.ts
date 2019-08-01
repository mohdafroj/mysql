import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { AccordionModule } 					from 'ngx-bootstrap';

import { FaqComponent }			 			from './faq/faq.component';

import { FaqRoutingModule } 			    from './faq-routing.module';

@NgModule({
  imports: [
	CommonModule,
	ReactiveFormsModule,
	FormsModule,
	AccordionModule.forRoot(),
	FaqRoutingModule
	],
  declarations:[
    FaqComponent
  ],
  exports:[
    FaqComponent
  ]
})
export class FaqModule { }
