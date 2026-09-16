import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['datePicker', 'timePicker', 'fileInput', 'fileImage']

    initialize() {
      this.onFocus = this.onFocus.bind(this)
      this.onBlur = this.onBlur.bind(this)
      this.onDateChange = this.onDateChange.bind(this)
      this.onTimeChange = this.onTimeChange.bind(this)
      this.onTimeInput = this.onTimeInput.bind(this)
      this.onFileChange = this.onFileChange.bind(this)
    }

    connect() {
        if (this.hasDatePickerTarget) {
                this.datePickerTarget.addEventListener("click",this.onFocus);
                this.datePickerTarget.addEventListener("blur",this.onBlur);
                this.datePickerTarget.addEventListener('change',this.onDateChange)
        }
        if (this.hasTimePickerTarget) {
                this.timePickerTarget.addEventListener("click",this.onFocus);
                this.timePickerTarget.addEventListener("blur",this.onBlur);
                this.timePickerTarget.addEventListener("change",this.onTimeChange);
                this.timePickerTarget.addEventListener("input",this.onTimeInput);
        }
        if (this.hasFileInputTarget) {
            this.fileInputTarget.addEventListener("change",this.onFileChange);
        }
    }

    disconnect() {
        if (this.hasDatePickerTarget) {
                this.datePickerTarget.removeEventListener("click",this.onFocus);
                this.datePickerTarget.removeEventListener("blur",this.onBlur);
                this.datePickerTarget.removeEventListener('change',this.onDateChange)
        }
        if (this.hasTimePickerTarget) {
                this.timePickerTarget.removeEventListener("click",this.onFocus);
                this.timePickerTarget.removeEventListener("blur",this.onBlur);
                this.timePickerTarget.removeEventListener("change",this.onTimeChange);
                this.timePickerTarget.removeEventListener("input",this.onTimeInput);
        }
        if (this.hasFileInputTarget) {
            this.fileInputTarget.removeEventListener("change",this.onFileChange);
        }
    }


    onFocus(e) {
        const element = e.target
        element.classList.add("focus");
    }

    
    onBlur(e) {
        const element = e.target
        element.classList.remove("focus");
    }


    onDateChange(e){
        const datePicker = e.target
        if (datePicker.value) {
            datePicker.classList.add("picker-newDate");
            const date = new Date(datePicker.value);
            datePicker.setAttribute("data-date", date.toLocaleDateString());
        } else {
            datePicker.removeAttribute("data-date");
            datePicker.setAttribute("class", "picker");
        }
        datePicker.classList.remove("focus");
    };


    onTimeInput(e){
        const timePicker = e.target
        if (timePicker.value) {
            timePicker.dataset.time = timePicker.value;
        }
    };


    onTimeChange(e){
        const timePicker = e.target
        if (timePicker.value) {
            timePicker.setAttribute("class", "time-picker time-picker-newTime");
            timePicker.dataset.time = timePicker.value;
        } else {
            timePicker.setAttribute("class", "time-picker");
        }
        timePicker.classList.remove("focus");
    };


    onFileChange(e){
        const fileInput = e.target
        if (fileInput.files[0]) {
            const image = URL.createObjectURL(fileInput.files[0]);
            this.fileImageTarget.src = image;
        } else {
            this.fileImageTarget.src = "";
        }
    };
}
