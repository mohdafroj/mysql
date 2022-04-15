import { NgModule }						 from '@angular/core';
import { RouterModule, Routes }			 from '@angular/router';
import { DisclaimersComponent }			 from './disclaimers/disclaimers.component';

const modRoutes: Routes = [
    {path:'', 					component: DisclaimersComponent, 			data:{id:0,title:'PerfumersClub: Disclaimers!'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class DisclaimersRoutingModule { }
