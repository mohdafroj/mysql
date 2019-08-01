import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { NguCarouselModule }          		from '@ngu/carousel';

import { PerfumeSelfieComponent }		 	from './perfume-selfie/perfume-selfie.component';
import { DetailComponent }		 		 	from './detail/detail.component';

import { SelfieRoutingModule } 			 	from './selfie-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,NguCarouselModule,SelfieRoutingModule],
  declarations:[
    PerfumeSelfieComponent,
	DetailComponent
  ],
  exports:[
    PerfumeSelfieComponent,
	DetailComponent
  ]
})
export class SelfieModule { }
