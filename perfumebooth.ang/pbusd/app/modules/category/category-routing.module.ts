import { NgModule }						 from '@angular/core';
import { RouterModule, Routes }			 from '@angular/router';
import { CategoryComponent }		     from './category/category.component';

const modRoutes: Routes = [
    {path:'', 					component: CategoryComponent, 			data:{id:0,title:'PerfumeBooth: Product Categpries!'}}
];

@NgModule({
  imports: [RouterModule.forChild(modRoutes)],
  exports: [RouterModule]
})
export class CategoryRoutingModule { }
