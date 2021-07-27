class autoCompleter {
    constructor(sourceArray, suggestionContainerID, inputId) {
        this.sourceArray = sourceArray;
        this.suggestionContainerID = suggestionContainerID;
        this.inputId = inputId;

        this.setupEvents();

        this.suggestionClicked = (xsuggest) => {
            let suggest     = jQuery(xsuggest.target).html();
            let input       = jQuery(this.inputId);
            let value       = input.val();
            let lastIndex   = value.lastIndexOf(',');
            let newInput    = lastIndex == -1? '' : value.substr(0,lastIndex) + ',';
    
            input.val(newInput + suggest);
            jQuery(this.suggestionContainerID).html('');
        };
    }
    

    setupEvents() {
        jQuery(this.inputId).keyup((xinput) => {
            let rolesInput  = jQuery(xinput.target).val().toLocaleLowerCase().replaceAll(' ','');
            let lastIndex   = rolesInput.lastIndexOf(',');
            let result      = []; 

            rolesInput = lastIndex == -1 ? rolesInput : rolesInput.substr(lastIndex + 1);
            for(let x of this.sourceArray) {
                if(rolesInput != '' && x.toLocaleLowerCase().includes(rolesInput) == true) {
                    result.push(`<a class="btn suggestedItem">${x}</a>`);
                }
            }
            jQuery(this.suggestionContainerID).html(result.join(', '));

            if(result.length)
                jQuery('.suggestedItem').click(this.suggestionClicked);
        });
    }
}
