import { Controller } from '@hotwired/stimulus';



/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['slider','secondSlider']

    connect() {
        if (!this.hasSliderTarget) return
        
        this.swiper = new Swiper(this.sliderTarget, {
            slidesPerView: 'auto',
            spaceBetween: 16,
            navigation:{
                nextEl: '.slider-button-next',
                prevEl: '.slider-button-prev',
            },
        });

        if (this.hasSecondSliderTarget) {
            this.swiperBottom = new Swiper(this.secondSliderTarget, {
            slidesPerView: 'auto',
            spaceBetween: 16,
        });
            this.swiper.controller.control = this.swiperBottom;
            this.swiperBottom.controller.control = this.swiper;
        }
    }   
   

    disconnect() {
       if (this.swiper) {
            this.swiper.destroy(true,true)
       }
       if (this.swiperBottom) {
             this.swiperBottom.destroy(true,true)
       }
    }
}
