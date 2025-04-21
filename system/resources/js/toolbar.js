function toolbarAdjustTop() {
    const toolbarHeight = document.getElementById("toolbar").offsetHeight;
    document.body.style.paddingTop = `${toolbarHeight}px`;
    const header = document.querySelector("header");
    const nav = document.querySelector("nav");

    [header, nav].forEach(element => {
        if (element && window.getComputedStyle(element).position === "fixed") {
            // Adjust the fixed header
            element.style.top = `${toolbarHeight}px`;
        }

        if (element) {
            const fixedElements = Array.from(element.querySelectorAll("*")).filter(el => 
                window.getComputedStyle(el).position === "fixed");
            fixedElements.forEach(el => {
                el.style.top = `${toolbarHeight}px`;
            });
        }
    });
}

window.addEventListener("load", toolbarAdjustTop);
window.addEventListener("resize", toolbarAdjustTop);