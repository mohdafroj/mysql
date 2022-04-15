import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { TermsAndConditionsComponent } from './terms-and-conditions/terms-and-conditions.component';

import { TermsConditionsRoutingModule } from './terms-conditions-routing.module';

@NgModule({
  imports: [CommonModule, ReactiveFormsModule, FormsModule, TermsConditionsRoutingModule],
  declarations: [
    TermsAndConditionsComponent
  ],
  exports: [
    TermsAndConditionsComponent
  ]
})
export class TermsConditionsModule { }
