import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { KnowMoreComponent }			 	from './know-more/know-more.component';

import { KnowMoreRoutingModule } 			from './know-more-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,KnowMoreRoutingModule],
  declarations:[
    KnowMoreComponent
  ],
  exports:[
    KnowMoreComponent
  ]
})
export class KnowMoreModule { }
