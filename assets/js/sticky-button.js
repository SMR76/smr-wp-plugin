// when document gets ready.
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('handbell').addEventListener('click', () => {
        let options = document.getElementsByClassName('sticky-option');
        for(let option of options) {
            option.classList.toggle("active");
        }
    });
});