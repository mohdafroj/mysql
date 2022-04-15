import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SwiperModule } from "swiper/angular";
import { ShopnowRoutingModule } from './shopnow-routing.module';
import { DashboardComponent } from './dashboard/dashboard.component';


@NgModule({
  declarations: [DashboardComponent],
  imports: [
    CommonModule,
    ShopnowRoutingModule,
	SwiperModule
  ]
})
export class ShopnowModule { }
