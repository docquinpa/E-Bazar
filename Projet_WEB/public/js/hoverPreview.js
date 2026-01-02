document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".ad-image").forEach(container => {

        const images = JSON.parse(container.dataset.images);
        if (!images || images.length <= 1) return;

        let index = 0;
        let interval = null;
        const img = container.querySelector("img");

        container.addEventListener("mouseenter", () => {
            interval = setInterval(() => {
                index = (index + 1) % images.length;
                img.src = BASE_URL + "uploads/" + images[index];
            }, 800);
        });

        container.addEventListener("mouseleave", () => {
            clearInterval(interval);
            img.src = BASE_URL + "uploads/" + images[0];
        });
    });
});
