import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StaticProductComponent } from './static-product.component';

describe('StaticProductComponent', () => {
  let component: StaticProductComponent;
  let fixture: ComponentFixture<StaticProductComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StaticProductComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StaticProductComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
