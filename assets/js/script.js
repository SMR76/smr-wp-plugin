
function toggle(){
    let options = document.getElementsByClassName('option');
    for(let x of options)
        x.classList.toggle("active");
}