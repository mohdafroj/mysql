import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardComponent } from './dashboard/dashboard.component';
import { LaunchOfferRoutingModule } from './launch-offer-routing.module';
//import { DealOfTheDayComponent } from './deal-of-the-day/deal-of-the-day.component';
//import { BuyOneGetOneComponent } from './buy-one-get-one/buy-one-get-one.component';


@NgModule({
  declarations: [
	DashboardComponent 
	//DealOfTheDayComponent, 
	//BuyOneGetOneComponent
  ],
  imports: [
    CommonModule,
	LaunchOfferRoutingModule
  ]
})
export class LaunchOfferModule { }
