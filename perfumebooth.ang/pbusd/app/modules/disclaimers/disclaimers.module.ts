import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { DisclaimersComponent }			 		from './disclaimers/disclaimers.component';

import { DisclaimersRoutingModule } 			from './disclaimers-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,DisclaimersRoutingModule],
  declarations:[
    DisclaimersComponent
  ],
  exports:[
    DisclaimersComponent
  ]
})
export class DisclaimersModule { }
