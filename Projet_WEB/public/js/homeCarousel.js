document.addEventListener("DOMContentLoaded", () => {

    const track = document.querySelector(".carousel-track");
    if (!track) return;

    const itemWidth = 240; // largeur d’un item
    let isAnimating = false;

    setInterval(() => {
        if (isAnimating) return;
        isAnimating = true;

        // Animation du slide
        track.style.transition = "transform 0.4s ease";
        track.style.transform = `translateX(-${itemWidth}px)`;

        // Une fois l’animation terminée, on remet proprement
        setTimeout(() => {
            track.style.transition = "none";

            // Déplacer le premier élément à la fin
            track.appendChild(track.firstElementChild);

            // Reset du translate
            track.style.transform = "translateX(0)";

            isAnimating = false;
        }, 400);

    }, 2500);

});
