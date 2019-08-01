import { NgModule }						 from '@angular/core';
import { RouterModule, Routes }			 from '@angular/router';
import { FaqComponent }			 		from './faq/faq.component';

const modRoutes: Routes = [
    {path:'', 					component: FaqComponent, 			data:{id:0,title:'PerfumeBooth: Frequently Asked Questions!'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class FaqRoutingModule { }
