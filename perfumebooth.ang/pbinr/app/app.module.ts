import { NoopAnimationsModule } from '@angular/platform-browser/animations';
import { BrowserModule, BrowserTransferStateModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { ToastrModule, ToastContainerModule  } 	from 'ngx-toastr';
import { MatExpansionModule } 					from '@angular/material/expansion';
import { MatAutocompleteModule} 				from '@angular/material/autocomplete';
import { MatFormFieldModule} 					from '@angular/material/form-field';
import { MatInputModule} 						from '@angular/material/input';
import { MatRadioModule} 						from '@angular/material/radio';

import { ReactiveFormsModule, FormsModule } 	from '@angular/forms';
import { HttpClientModule,HTTP_INTERCEPTORS } 	from '@angular/common/http';

import { AccordionModule } 						from 'ngx-bootstrap';
import { BsDropdownModule } 					from 'ngx-bootstrap/dropdown';
import { NguCarouselModule }          			from '@ngu/carousel';
import { ProductModule } from './featured/product/product.module';

//services
import { PBInterceptor } 	                    from './_services/pb.interceptor';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './components/home/home.component';
import { HeaderComponent } from './layout/header/header.component';
import { FooterComponent } from './layout/footer/footer.component';
import { MiniCartComponent } from './layout/mini-cart/mini-cart.component';
import { SearchComponent } from './layout/search/search.component';
import { DetailsComponent } 					from './components/products/details/details.component';
import { BrandsComponent } 					    from './components/brand/brands.component';
import { BrandPerfumesComponent } 			    from './components/brand/perfume/perfume.component';
import { SaleComponent } from './components/products/sale/sale.component';
import { CmsComponent } from './components/cms/cms.component';
import { ServiceWorkerModule } from '@angular/service-worker';
import { environment } from '../environments/environment';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    HeaderComponent,
    FooterComponent,
    MiniCartComponent,
    SearchComponent,
	DetailsComponent,
	BrandsComponent,
	BrandPerfumesComponent,
	SaleComponent,
	CmsComponent
  ],
  imports: [
    BrowserModule,
    NoopAnimationsModule,
    BrowserTransferStateModule,
	HttpClientModule,
	FormsModule,
	ReactiveFormsModule,
    HttpClientModule,
	ToastrModule.forRoot({
		progressBar:true,
		progressAnimation:'increasing'
	}),
	ToastContainerModule,
	AccordionModule.forRoot(),
	BsDropdownModule.forRoot(),
	MatAutocompleteModule,
	MatFormFieldModule,
	MatInputModule,
	MatRadioModule,
	MatExpansionModule,
    NguCarouselModule,
    AppRoutingModule,
	ProductModule,
    ServiceWorkerModule.register('ngsw-worker.js', { enabled: environment.production })
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: PBInterceptor, multi: true }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
