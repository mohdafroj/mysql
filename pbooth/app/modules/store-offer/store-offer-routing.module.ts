import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SaleComponent } from './sale/sale.component';
import { ClearanceComponent } from './clearance/clearance.component';

const routes: Routes = [
	{
    path:'', 	component: SaleComponent, data:{id:0, title:'Buy Online Perfume Fragrance | Perfume For Men and Women'}
  },
	{
    path:'products', 	component: ClearanceComponent, data:{id:0, title:'Buy Online Perfume Fragrance | Perfume For Men and Women'}
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class StoreOfferRoutingModule { }
