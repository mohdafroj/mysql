import { NgModule } 									from '@angular/core';
import { CommonModule } 								from '@angular/common';
import { ReactiveFormsModule, FormsModule } 			from '@angular/forms';

import { AboutUsComponent } 							from './about-us/about-us.component';

import { AboutUsRoutingModule } 						from './about-us-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,AboutUsRoutingModule],
  declarations:[
    AboutUsComponent
  ],
  exports:[
    AboutUsComponent
  ]
})
export class AboutUsModule { }
