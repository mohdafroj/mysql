import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PerfumesComponent } from './perfumes.component';

describe('PerfumesComponent', () => {
  let component: PerfumesComponent;
  let fixture: ComponentFixture<PerfumesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PerfumesComponent ]
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
