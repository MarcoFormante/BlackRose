
import './styles/admin/index.css';

// Select the EasyAdmin file upload card container element
const imageIconContainer = document.querySelector(".ea-fileupload-card")


if (imageIconContainer) {
    // Retrieve the target file name and default preview icon element
    const fileName = document.querySelector(".ea-fileupload-card-name")
    const icon = document.querySelector(".ea-fileupload-card-preview");
    // Remove the generic preview icon from the upload container
    imageIconContainer.removeChild(icon)
    // Create a new image element pointing to the uploaded artist image path
    const newImage = new Image()
    newImage.src = "/uploads/artists/" + fileName.textContent
    newImage.classList.add("artist-image")
    // Insert the actual image preview at the top of the container
    imageIconContainer.prepend(newImage)
}


