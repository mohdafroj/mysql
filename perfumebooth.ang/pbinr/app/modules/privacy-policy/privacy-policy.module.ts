import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { PrivacyPolicyComponent } from './privacy-policy/privacy-policy.component';

import { PrivacyPolicyRoutingModule } from './privacy-policy-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,PrivacyPolicyRoutingModule],
  declarations:[
    PrivacyPolicyComponent
  ],
  exports:[
    PrivacyPolicyComponent
  ]
})
export class PrivacyPolicyModule { }
