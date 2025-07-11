import "./bootstrap";

import Alpine from "alpinejs";

import SimpleBar from "simplebar";

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    const offcanvasBody = document.querySelector(".offcanvas-body");
    if (offcanvasBody) {
        new SimpleBar(offcanvasBody, {
            autoHide: false, // Keep scrollbar visible
        });
    }
});

try {
    localStorage.setItem("key", "value");
} catch (e) {
    console.error("Storage access denied:", e);
}
