import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } 	from '@angular/forms';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { NguCarouselModule } from '@ngu/carousel';
import { ReviewsComponent } from './reviews/reviews.component';
import { NotifymeComponent } from './notifyme/notifyme.component';
import { PopupProductComponent } from './popup-product/popup-product.component';
import { RelatedProductComponent } from './related-product/related-product.component';
import { DeleteComponent } from './delete/delete.component';

@NgModule({
  imports: [
    CommonModule,
    MatProgressBarModule,
	ReactiveFormsModule,
	NguCarouselModule,
	FormsModule
  ],
  declarations: [
	ReviewsComponent,
	NotifymeComponent,
	PopupProductComponent,
	RelatedProductComponent,
	DeleteComponent
  ],
  exports: [
    ReviewsComponent,
	NotifymeComponent,
	PopupProductComponent,
	RelatedProductComponent,
	DeleteComponent
  ]
})

export class ProductModule { }
