document.addEventListener("DOMContentLoaded", () => {

    const track = document.querySelector(".carousel-track");
    if (!track) return;

    const items = track.children;
    if (items.length <= 1) return;

    let index = 0;

    setInterval(() => {
        index = (index + 1) % items.length;
        track.style.transform = `translateX(-${index * 240}px)`;
    }, 2500);

});
