import { NgModule }						 from '@angular/core';
import { RouterModule, Routes }			 from '@angular/router';
import { PrivacyPolicyComponent }				 from './privacy-policy/privacy-policy.component';

const modRoutes: Routes = [
    {path:'', 					component: PrivacyPolicyComponent, 			data:{id:0,title:'Perfumer\'s Club: Privacy Policy!'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class PrivacyPolicyRoutingModule { }
