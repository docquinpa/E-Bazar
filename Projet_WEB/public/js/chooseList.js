document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("sectionSelect");
    const sections = document.querySelectorAll(".sect");

    if (!select || sections.length === 0) {
        return;
    }

    function updateSections() {
        const value = select.value;
        sections.forEach(sec => sec.classList.remove("active"));

        const target = document.getElementById("section-" + value);
        if (target) {
            target.classList.add("active");
        }
    }

    select.addEventListener("change", updateSections);
    updateSections();
});
