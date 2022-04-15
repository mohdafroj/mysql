import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { RouterModule } from '@angular/router';
import { HttpClientModule } 	from '@angular/common/http';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { NguCarouselModule } from '@ngu/carousel';
import { ToastrModule } 	from 'ngx-toastr';
import { RelatedProductComponent } from './related-product.component';

describe('RelatedProductComponent', () => {
  let component: RelatedProductComponent;
  let fixture: ComponentFixture<RelatedProductComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RelatedProductComponent ],
	  imports: [ RouterModule.forRoot([]), NguCarouselModule, ToastrModule.forRoot(), HttpClientModule, ReactiveFormsModule, FormsModule ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RelatedProductComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
