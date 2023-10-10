document.addEventListener("DOMContentLoaded", function (e) {
    var scrollpos = sessionStorage.getItem('scrollpos');
    if (scrollpos) {
        window.scrollTo(0, scrollpos);
        sessionStorage.removeItem('scrollpos');
        sessionStorage.setItem('scrolled', true);
    }
});

window.addEventListener("load", function(f) {
    var scrolled = sessionStorage.getItem('scrolled');
    if (scrolled) {
        window.scrollTo(0, document.body.scrollHeight);
        sessionStorage.removeItem('scrolled');    
    }
});

window.addEventListener("beforeunload", function (g) {
    sessionStorage.setItem('scrollpos', window.scrollY);
});