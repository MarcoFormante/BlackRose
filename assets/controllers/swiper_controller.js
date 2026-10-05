import { Controller } from '@hotwired/stimulus';
import Swiper from 'swiper';
import {Grid,Navigation} from "swiper/modules"

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['slider']

     static values = {
        slideWidth: { default: 260 },
        gap: {  default: 16 },
        rows: {  default: 2 },   
    };


    initialize(){
        this.showLightBox = this.showLightBox.bind(this)
        this.perView = this.perView.bind(this)
    }

    connect() {
        this.beforeCache = () => this.destroy();
        document.addEventListener('turbo:before-cache', this.beforeCache);
        this.element.classList.add('hide-on-start-off');
        
        if (!this.hasSliderTarget) return
        
        if (!this.sliderTarget.classList.contains("swiper-mix")) {
                this.swiper = new Swiper(this.sliderTarget, {
                modules:[Navigation],
                slidesPerView: 'auto',
                resistance: true,
                freeMode: false,
                navigation:{
                    nextEl: '.slider-button-next',
                    prevEl: '.slider-button-prev',
                },
                 on: {
                        click:this.showLightBox
                    },
                });
        }else{
        
            this.swiper = new Swiper(this.sliderTarget, {
                    modules: [Navigation, Grid],
                    spaceBetween: this.gapValue,
                    slidesPerView: this.perView(),
                    resistance: true,
                    freeMode: false,
                    grid: { rows: 1 },
                    breakpoints: {
                        700: {  slidesPerView: this.perView(), grid: { rows: this.rowsValue, fill: 'row' } },
                    },
                    navigation: {
                        nextEl: this.element.querySelector('.slider-button-next'),
                        prevEl: this.element.querySelector('.slider-button-prev'),
                    },
                    on: {
                        resize: (swiper) => {
                            swiper.params.slidesPerView = this.perView();
                            swiper.update()
                        },
                        click:this.showLightBox
                    },
                });
                
            }

           
    }

    perView() {
        const width = this.sliderTarget.clientWidth;
        return Math.max(1, (width + this.gapValue) / (this.slideWidthValue + this.gapValue)).toFixed(1);
    }
       
    destroy() {
        this.swiper?.destroy(true, true);
        this.swiper = null;
    }
       
       

    disconnect() {
        document.removeEventListener('turbo:before-cache', this.beforeCache);
        this.destroy();
    }


    showLightBox(sw){
            const clickedSlide = sw.clickedSlide;
            
            if (!clickedSlide.dataset?.index ) {
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
                        image.src = slide.dataset.full ?? slide.querySelector('img').src
                        image.loading = "lazy"
                        image.decoding = "async"
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
