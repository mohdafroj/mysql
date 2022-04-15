import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { WhyRegisterComponent } 			from './why-register/why-register.component';
import { WhyRegisterRoutingModule } 		from './why-register-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,WhyRegisterRoutingModule],
  declarations:[
    WhyRegisterComponent
  ],
  exports:[
    WhyRegisterComponent
  ]
})
export class WhyRegisterModule { }
