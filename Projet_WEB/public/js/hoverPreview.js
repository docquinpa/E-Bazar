document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".ad-image").forEach(container => {

        const images = JSON.parse(container.dataset.images);
        if (!images || images.length <= 1) return;

        let index = 0;
        const img = container.querySelector("img");

        const leftBtn = container.querySelector(".left");
        const rightBtn = container.querySelector(".right");

        function updateImage() {
            img.src = BASE_URL + "upload/" + images[index];
        }

        // Flèche droite
        rightBtn.addEventListener("click", e => {
            e.stopPropagation();
            index = (index + 1) % images.length;
            updateImage();
        });

        // Flèche gauche
        leftBtn.addEventListener("click", e => {
            e.stopPropagation();
            index = (index - 1 + images.length) % images.length;
            updateImage();
        });
    });
});
