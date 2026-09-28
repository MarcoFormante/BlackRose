import { Controller } from '@hotwired/stimulus';



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
                        slidesPerView: 1,
                        spaceBetween: 16,
                        navigation:{
                            nextEl: '.swiper-button-next-lightbox',
                            prevEl: '.swiper-button-prev-lightbox',
                        },
                        lazy:{
                            enable:true
                        },
                    });

            this.swiper.slideTo(this.lightboxTarget.dataset.clickedSlide,0)

            if (this.hasExitTarget) {
                this.exitTarget.addEventListener("click",this.closeLightBox)
            }
    }


    disconnect() {
        if (this.swiper) {
            this.swiper.destroy(true,true)
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
        this.lightboxTarget.classList.remove("swiper-lightBox-active")
        this.element.removeAttribute("data-controller")
    }
}