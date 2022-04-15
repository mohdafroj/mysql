import { NgModule }				from '@angular/core';
import { RouterModule, Routes }	from '@angular/router';
import { DashboardComponent } 	from './dashboard/dashboard.component';
import { DealOfTheDayComponent } from './deal-of-the-day/deal-of-the-day.component';
import { BuyOneGetOneComponent } from './buy-one-get-one/buy-one-get-one.component';

const modRoutes: Routes = [
    { path:'', 					component: DashboardComponent, 	  data:{id:0, title:'Launch offer | Original Branded Fragrance Shop'}},
	{ path:'buy-one-get-one', 	component: BuyOneGetOneComponent, data:{id:0, title:'Launch offer | Buy One Get One'}},
	{ path:'deal-of-the-day', 	component: DealOfTheDayComponent, data:{id:0, title:'Launch offer | Deal Of The Day'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class LaunchOfferRoutingModule { }
