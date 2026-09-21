import { Controller } from '@hotwired/stimulus';



/* stimulusFetch: 'lazy' */
export default class extends Controller {
    // Define target elements for main and secondary Swiper instances
    static targets = ['slider','secondSlider']

    /**
     * Initializes Swiper instances and configures dual-slider synchronization upon DOM connection.
     */
    connect() {
        if (!this.hasSliderTarget) return
        // Initialize the primary Swiper carousel
        this.swiper = new Swiper(this.sliderTarget, {
            slidesPerView: 'auto',
            spaceBetween: 16,
            navigation:{
                nextEl: '.slider-button-next',
                prevEl: '.slider-button-prev',
            },
            lazy:{
                enable:true
            }
        });

        // Initialize the secondary Swiper carousel if present and synchronize controls bidirectionally
        if (this.hasSecondSliderTarget) {
            this.swiperBottom = new Swiper(this.secondSliderTarget, {
            slidesPerView: 'auto',
            spaceBetween: 16,
            lazy:{
                enable:true
            }
        });
        // Bind slider instances together so scrolling one updates the other
            this.swiper.controller.control = this.swiperBottom;
            this.swiperBottom.controller.control = this.swiper;
        }
    }   
   
    /**
     * Destroys Swiper instances to prevent memory leaks when the controller is disconnected from the DOM.
     */
    disconnect() {
       if (this.swiper) {
            this.swiper.destroy(true,true)
       }
       if (this.swiperBottom) {
             this.swiperBottom.destroy(true,true)
       }
    }
}
