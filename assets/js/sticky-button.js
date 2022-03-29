
function toggle(){
    let options = document.getElementsByClassName('smr-sticky-button-option');
    for(let x of options)
        x.classList.toggle("active");
}