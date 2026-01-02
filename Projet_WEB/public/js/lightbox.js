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

    leftArrow.addEventListener("click", () => {
        currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
        updateLightbox();
    });

    rightArrow.addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % currentImages.length;
        updateLightbox();
    });


    // --- Intégration avec ton viewer ---
    document.querySelectorAll(".photo-viewer").forEach(viewer => {

        const images = JSON.parse(viewer.dataset.images);
        const img = viewer.querySelector(".viewer-img");

        img.addEventListener("click", () => {
            openLightbox(images, 0);
        });

        viewer.querySelector(".viewer-arrow.left").addEventListener("click", () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            img.src = BASE_URL + "upload/" + images[currentIndex];
        });

        viewer.querySelector(".viewer-arrow.right").addEventListener("click", () => {
            currentIndex = (currentIndex + 1) % images.length;
            img.src = BASE_URL + "upload/" + images[currentIndex];
        });
    });
});
