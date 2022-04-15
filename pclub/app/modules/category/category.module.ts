import { NgModule } 						from '@angular/core';
import { CommonModule } 					from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { CategoryComponent }		        from './category/category.component';

import { CategoryRoutingModule } 			from './category-routing.module';

@NgModule({
  imports: [CommonModule,ReactiveFormsModule,FormsModule,CategoryRoutingModule],
  declarations:[
    CategoryComponent
  ],
  exports:[
    CategoryComponent
  ]
})
export class CategoryModule { }
