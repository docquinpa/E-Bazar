document.addEventListener("DOMContentLoaded", () => {

    // --- Lightbox global ---
    const lightbox = document.getElementById("lightbox");
    const lightboxImg = document.getElementById("lightbox-img");
    const closeBtn = document.querySelector(".close-lightbox");
    const leftArrow = document.querySelector(".lightbox-arrow.left");
    const rightArrow = document.querySelector(".lightbox-arrow.right");

    let currentImages = [];
    let currentIndex = 0;

    function openLightbox(images, index) {
        currentImages = images;
        currentIndex = index;
        lightboxImg.src = BASE_URL + "upload/" + images[index];
        lightbox.classList.remove("hidden");
    }

    function updateLightbox() {
        lightboxImg.src = BASE_URL + "upload/" + currentImages[currentIndex];
    }

    closeBtn.addEventListener("click", () => {
        lightbox.classList.add("hidden");
    });

    lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox) {
            lightbox.classList.add("hidden");
        }
    });

    leftArrow.addEventListener("click", e => {
        e.stopPropagation();
        currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
        updateLightbox();
    });

    rightArrow.addEventListener("click", e => {
        e.stopPropagation();
        currentIndex = (currentIndex + 1) % currentImages.length;
        updateLightbox();
    });


    // --- Nouvelle galerie simple ---
    const galleryImages = Array.from(document.querySelectorAll(".ad-photo"));

    // Récupère juste les noms de fichiers
    const imageList = galleryImages.map(img => img.dataset.filename);

    galleryImages.forEach((img, index) => {
        img.addEventListener("click", () => {
            openLightbox(imageList, index);
        });
    });

});
