import { Controller } from '@hotwired/stimulus';



/* stimulusFetch: 'lazy' */
export default class extends Controller {
    // Define target elements for main and secondary Swiper instances
    static targets = ['slider','secondSlider']

    initialize(){
        this.showLightBox = this.showLightBox.bind(this)
    }

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
            },
        });
            this.swiper.on('click', this.showLightBox);

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
            this.swiperBottom.on('click', this.showLightBox)
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


    showLightBox(sw){
            if (!sw.clickedSlide) {
                return
            }
            
            const container = document.querySelector(".swiper-wrapper-lightBox")
            if (!container.innerHTML) {
                let index = sw.clickedSlide.dataset.index
             
                const lightBox = document.querySelector(".swiper-lightBox");
                const slides = sw.slides
                
                slides.forEach(slide => {
                    if(slide.hasChildNodes()){
                        const image = document.createElement("img")
                        image.src = slide.querySelector("img").src
                        const swiperSlide = document.createElement("div")
                        swiperSlide.classList.add("swiper-slide")
                        swiperSlide.classList.add("swiper-slide-lightBox")
                        swiperSlide.appendChild(image)
                        container.appendChild(swiperSlide)
                    }
                });
                if (this.hasSecondSliderTarget) {
                    const bottomSlides = this.swiperBottom.slides
                    bottomSlides.forEach(slide => {
                        if(slide.hasChildNodes()){
                            const image = document.createElement("img")
                            image.src = slide.querySelector("img").src
                            const swiperSlide = document.createElement("div")
                            swiperSlide.classList.add("swiper-slide")
                            swiperSlide.classList.add("swiper-slide-lightBox")
                            swiperSlide.appendChild(image)
                            container.appendChild(swiperSlide)
                        }else{
                           if(sw.el.classList.contains("slider-bottom")  ){
                                index--
                            } 
                        }
                    });
                }
                
                
                lightBox.classList.add("swiper-lightBox-active")
                lightBox.dataset.clickedSlide = index 
                document.querySelector("#lightbox").setAttribute("data-controller","lightbox")
        }
    }
}
