import { NgModule } from '@angular/core';
import { RouterModule, Routes }	from '@angular/router';
import { ContentComponent }	from './content/content.component';
import { ProductsComponent } from './products/products.component';
import { RefillsComponent }	from './refills/refills.component';
import { PerfumesComponent } from './perfumes/perfumes.component';

const modRoutes: Routes = [
    {path: '100ml-perfumes', 			component: PerfumesComponent, 			data: {id: 0, title: 'PerfumeBooth: Scent Shot: 10ML Perfumes!'} },
    {path: 'refills', 					component: RefillsComponent, 			data: {id: 0, title: 'PerfumeBooth: Scent Shot: Refills!'} },
    {path: 'scent-shot-perfumes', 		component: ProductsComponent, 			data: {id: 0, title: 'PerfumeBooth: Scent Shot: Products!'} },
    {path: '', 							component: ContentComponent, 			data: {id: 0, title: 'PerfumeBooth: Scent Shot!'} }
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class ScentShotRoutingModule { }
