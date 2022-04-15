import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BuyOneGetOneComponent } from './buy-one-get-one.component';

describe('BuyOneGetOneComponent', () => {
  let component: BuyOneGetOneComponent;
  let fixture: ComponentFixture<BuyOneGetOneComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BuyOneGetOneComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BuyOneGetOneComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
