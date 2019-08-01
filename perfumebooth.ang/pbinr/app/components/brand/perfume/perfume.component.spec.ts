/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { BrandPerfumesComponent } from './perfume.component';

describe('BrandPerfumesComponent', () => {
  let component: BrandPerfumesComponent;
  let fixture: ComponentFixture<BrandPerfumesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BrandPerfumesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BrandPerfumesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
