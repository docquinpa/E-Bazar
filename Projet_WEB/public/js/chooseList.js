document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("sectionSelect");
    const sections = document.querySelectorAll(".sect");
    function updateSections() {
        const value = select.value;
        sections.forEach(sec => sec.classList.remove("active"));
        document.getElementById("section-" + value).classList.add("active");
    } select.addEventListener("change", updateSections);
    updateSections();
});