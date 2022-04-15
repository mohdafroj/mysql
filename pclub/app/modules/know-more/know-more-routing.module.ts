import { NgModule }						 from '@angular/core';
import { RouterModule, Routes }			 from '@angular/router';
import { KnowMoreComponent }			 from './know-more/know-more.component';

const modRoutes: Routes = [
    {path:'', 					component: KnowMoreComponent, 			data:{id:0,title:'PerfumersClub: Know More!'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class KnowMoreRoutingModule { }
