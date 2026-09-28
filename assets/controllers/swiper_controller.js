import { Controller } from '@hotwired/stimulus';



/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['slider']

    initialize(){
        this.showLightBox = this.showLightBox.bind(this)
    }

    connect() {
        this.element.classList.add('hide-on-start-off');
        
        if (!this.hasSliderTarget) return
        // Initialize the primary Swiper carousel
        if (!this.sliderTarget.classList.contains("swiper-mix")) {
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
        }else{
            this.swiper = new Swiper(this.sliderTarget, {
                modules: [Swiper.Grid, Swiper.Navigation],
                slidesPerView: 'auto',
                spaceBetween: 16,
                grid: {
                    rows: 1,
                    fill: 'row',
                },
                breakpoints: {
                    768: { 
                    grid: {
                        rows: 2, 
                    },
                    },
                },
                navigation:{
                    nextEl: '.slider-button-next',
                    prevEl: '.slider-button-prev',
                },
                    
            });
        }
       
        this.swiper.on('click', this.showLightBox);
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
                lightBox.classList.add("swiper-lightBox-active")
                lightBox.dataset.clickedSlide = index 
                document.querySelector("#lightbox").setAttribute("data-controller","lightbox")
        }
    }
}
