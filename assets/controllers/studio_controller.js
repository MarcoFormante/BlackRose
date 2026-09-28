import { Controller } from '@hotwired/stimulus';



/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['showBtn']

    initialize(){
        this.showMore = this.showMore.bind(this)
    }

    connect() {
        this.showBtnTarget.addEventListener('click',this.showMore)
        this.images = this.element.querySelectorAll('.hide-image-mobile')
        this.clicked = false
        this.scrollTarget = this.element.querySelector('#scrollTarget')
        this.showBtnTarget.disabled = false
    }


    disconnect(){
        this.showBtnTarget.removeEventListener('click',this.showMore)
    }

    showMore(){
        if (!this.clicked) {
            this.showBtnTarget.innerText = "MOSTRA MENO FOTO"
        }else{
            this.showBtnTarget.innerText = "CARICA ALTRE FOTO"   
        }

        this.showBtnTarget.setAttribute("aria-expanded",!this.clicked)
        
        this.images.forEach(image => {
            image.classList.toggle("hide-image-mobile")
        });

        if (this.clicked) {
            this.scrollTarget.scrollIntoView()
        }

        this.clicked = !this.clicked
    }

}