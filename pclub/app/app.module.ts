import { NoopAnimationsModule, BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { BrowserModule, BrowserTransferStateModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { ToastrModule, ToastContainerModule  } 	from 'ngx-toastr';
import { MatExpansionModule } 					from '@angular/material/expansion';
import { MatAutocompleteModule} 				from '@angular/material/autocomplete';
import { MatFormFieldModule} 					from '@angular/material/form-field';
import { MatInputModule} 						from '@angular/material/input';
import { SwiperModule } from "swiper/angular";
import { ReactiveFormsModule, FormsModule } 	from '@angular/forms';
import { HttpClientModule,HTTP_INTERCEPTORS } 	from '@angular/common/http';

import { AccordionModule } 						from 'ngx-bootstrap/accordion';
import { BsDropdownModule } 					from 'ngx-bootstrap/dropdown';
import { SlickCarouselModule } from 'ngx-slick-carousel';
import { ProductModule } from './featured/product/product.module';

//services
import { PBInterceptor } from './_services/pb.interceptor';
import { CookieService } from 'ngx-cookie-service';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './components/home/home.component';
import { HeaderComponent } from './layout/header/header.component';
import { FooterComponent } from './layout/footer/footer.component';
import { MiniCartComponent } from './layout/mini-cart/mini-cart.component';
import { AccountComponent } from './components/account/account.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { CmsComponent } from './components/cms/cms.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    HeaderComponent,
    FooterComponent,
    MiniCartComponent,
	LoginComponent,
	RegisterComponent,
	AccountComponent,
	CmsComponent
  ],
  imports: [
    BrowserModule,
    NoopAnimationsModule,
	BrowserAnimationsModule,
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
	MatExpansionModule,
	SlickCarouselModule,
	SwiperModule,
    AppRoutingModule,
	ProductModule
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: PBInterceptor, multi: true }, CookieService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
