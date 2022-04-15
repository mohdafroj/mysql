import { async, ComponentFixture, TestBed, inject } from '@angular/core/testing';

import { CmsComponent } from './cms.component';
import { DebugElement } from '@angular/core';
import { NguCarouselModule } from '@ngu/carousel';
import { RouterModule } from '@angular/router';
import { HttpClientModule } 	from '@angular/common/http';
import { ReactiveFormsModule } 	from '@angular/forms';
import { ProductModule } from './../../featured/product/product.module';
import { ToastrModule, ToastContainerModule, ToastrService } 	from 'ngx-toastr';
import 'hammerjs';

describe('CmsComponent', () => {
  let component: CmsComponent;
  let fixture: ComponentFixture<CmsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CmsComponent ],
	  imports: [ NguCarouselModule, RouterModule.forRoot([]), HttpClientModule, ReactiveFormsModule, ProductModule, ToastrModule.forRoot({
		progressBar:true,
		progressAnimation:'increasing'
	}), ToastContainerModule ],
	  providers: [ ToastrService ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CmsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
  
});
