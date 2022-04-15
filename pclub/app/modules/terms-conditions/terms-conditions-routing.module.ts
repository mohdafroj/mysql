import { NgModule }						 from '@angular/core';
import { RouterModule, Routes }			 from '@angular/router';
import { TermsAndConditionsComponent }	 from './terms-and-conditions/terms-and-conditions.component';

const modRoutes: Routes = [
    {path:'', 					component: TermsAndConditionsComponent, 			data:{id:0,title:'Perfumer\'s Club: Terms and Conditions!'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class TermsConditionsRoutingModule { }
