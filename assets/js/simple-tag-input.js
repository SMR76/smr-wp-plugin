function createTagElement(htmlValue, mainInput) {
    let tag = document.createElement('label', {});
    tag.classList.add('tag')
    tag.addEventListener('click', function() {
        const regex = new RegExp(`(\\b|,)${this.innerHTML}\\b`,'g');
        mainInput.value = mainInput.value.replace(regex,""); // remove tag from input value
        this.remove(); // remove tag
    });
    tag.innerHTML = htmlValue;
    return tag;
}

function smrSimpleTagInput() {
    let tagInputs = document.querySelectorAll('[tagged]');
    for(let input of tagInputs) {
        input.setAttribute('hidden','hidden');
        let fakeInput = document.createElement('input',{type: 'text'});

        fakeInput.classList = input.classList;
        fakeInput.mainPair = input;
        fakeInput.setAttribute('type','text');
        fakeInput.setAttribute('fakeinput','');
        fakeInput.placeholder = input.placeholder ?? "";

        fakeInput.addEventListener('keydown', function(e) {
            const trValue = this.value.trim();
            if( ['NumpadEnter','Tab','Space','Enter'].includes(e.code) && trValue.length) {
                if(!this.mainPair.value.match(`\\b${trValue}\\b`)) {
                    let tag = createTagElement(trValue, this.mainPair);
                    this.mainPair.value += ',' + trValue;
                    this.after(tag); // add tag before input element.
                }
                this.value = ""; // reset value.
                e.preventDefault();
            }
        })

        input.after(fakeInput);

        if(input.value.trim().length) {
            let initVal = input.value.trim().split(',');
            for(let val of initVal) {
                if(val.length) {
                    let tag = createTagElement(val, input);
                    fakeInput.after(tag);
                }
            }
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    smrSimpleTagInput(window.MultiselectDropdownOptions);
});