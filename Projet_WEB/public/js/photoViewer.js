document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".photo-viewer").forEach(viewer => {

        const images = JSON.parse(viewer.dataset.images);
        if (!images || images.length <= 1) return;

        let index = 0;
        const img = viewer.querySelector(".viewer-img");

        const left = viewer.querySelector(".viewer-arrow.left");
        const right = viewer.querySelector(".viewer-arrow.right");

        function update() {
            img.src = BASE_URL + "upload/" + images[index];
        }

        right.addEventListener("click", e => {
            e.stopPropagation();
            index = (index + 1) % images.length;
            update();
        });

        left.addEventListener("click", e => {
            e.stopPropagation();
            index = (index - 1 + images.length) % images.length;
            update();
        });
    });
});
