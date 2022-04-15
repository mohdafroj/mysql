import { NgModule } from '@angular/core';
import { RouterModule, Routes }	from '@angular/router';
import { ProductsComponent } from './products/products.component';

const modRoutes: Routes = [
	  { path: '', 		redirectTo: 'products'},
    { path: 'products', 		component: ProductsComponent, 			data: {id: 0, title: 'PerfumeBooth: Lightr: Products!'} }
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class LightrRoutingModule { }
