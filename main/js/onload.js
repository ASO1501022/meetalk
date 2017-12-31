function addOnload(a) {
try {
        window.addEventListener("load", a, false);
    } catch (e) {
        // IE用 
        window.attachEvent("onload", a);
    }
}