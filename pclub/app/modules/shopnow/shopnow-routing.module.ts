import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardComponent } from './dashboard/dashboard.component';

const routes: Routes = [
  {path:'', 	component: DashboardComponent, data:{id:0,title:'Perfumer\'s Club: Shop Now!'}},
  {path:':key', 	component: DashboardComponent, data:{id:0,title:'Perfumer\'s Club: Shop Now!'}}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ShopnowRoutingModule { }
