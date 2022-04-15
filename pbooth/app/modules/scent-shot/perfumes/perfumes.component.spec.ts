import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { NguCarouselModule } from '@ngu/carousel';
import { NoopAnimationsModule } from '@angular/platform-browser/animations';
import { RouterModule } from '@angular/router';
import { HttpClientModule } 	from '@angular/common/http';
import { ProductModule } from './../../../featured/product/product.module';
import { ToastrModule } 	from 'ngx-toastr';
import { PerfumesComponent } from './perfumes.component';

describe('PerfumesComponent', () => {
  let component: PerfumesComponent;
  let fixture: ComponentFixture<PerfumesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PerfumesComponent ],
	  imports: [NoopAnimationsModule, NguCarouselModule, ProductModule, RouterModule.forRoot([]), ToastrModule.forRoot(), HttpClientModule ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PerfumesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
