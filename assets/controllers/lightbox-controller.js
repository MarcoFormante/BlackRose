import { Controller } from '@hotwired/stimulus';
import Swiper from "swiper"
import { Navigation, Pagination } from 'swiper/modules';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['lightbox','exit','wrapper']

    initialize(){
        this.closeLightBox = this.closeLightBox.bind(this)
    }

    connect(){
        if (!this.hasLightboxTarget) {
            return 
        }
        
        this.swiper = new Swiper(this.lightboxTarget, {
                        modules:[Navigation],
                        slidesPerView: 1,
                        spaceBetween: 16,
                        initialSlide:this.lightboxTarget.dataset.clickedSlide,
                        navigation:{
                            nextEl: '.swiper-button-next-lightbox',
                            prevEl: '.swiper-button-prev-lightbox',
                        },
                    });


            if (this.hasExitTarget) {
                this.exitTarget.addEventListener("click",this.closeLightBox)
            }
    }


    disconnect() {
        if (this.swiper) {
            this.swiper.destroy(true,true)
            this.swiper = null
        }
        if (this.hasExitTarget) {
            this.exitTarget.removeEventListener("click", this.closeLightBox)
        }
    }

    closeLightBox(){
        if (!this.hasWrapperTarget) {
            return 
        }
        this.wrapperTarget.innerHTML = ""
        this.swiper.destroy(true,true)
        this.lightboxTarget.classList.remove("swiper-lightBox-active")
        this.element.removeAttribute("data-controller")
    }
}