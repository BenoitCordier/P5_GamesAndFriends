window.onload = document.getElementById('dropdownNav').addEventListener('click', (event) => {
    event.preventDefault();
    var navVisible = sessionStorage.getItem('navVisible');
    if (!navVisible) {
        document.getElementById('dropdownMenu').style.display = 'block';
        sessionStorage.setItem('navVisible', true);
    } else {
        document.getElementById('dropdownMenu').style.display = 'none';
        sessionStorage.removeItem('navVisible');
    }
}, false);

