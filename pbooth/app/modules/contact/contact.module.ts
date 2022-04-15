import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { ContactComponent }			 		from './contact/contact.component';

import { ContactRoutingModule } 			from './contact-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,ContactRoutingModule],
  declarations:[
    ContactComponent
  ],
  exports:[
    ContactComponent
  ]
})
export class ContactModule { }
