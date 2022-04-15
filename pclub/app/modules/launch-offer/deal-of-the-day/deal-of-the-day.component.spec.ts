import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { DealOfTheDayComponent } from './deal-of-the-day.component';

describe('DealOfTheDayComponent', () => {
  let component: DealOfTheDayComponent;
  let fixture: ComponentFixture<DealOfTheDayComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DealOfTheDayComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DealOfTheDayComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
