// when document gets ready.
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('handbell').addEventListener('click', () => {
        let options = document.getElementsByClassName('smr-sticky-button-option');
        for(let x of options) {
            x.classList.toggle("active");
        }
    });
});