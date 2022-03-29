

window.onload = () => {
    if (jQuery("#activate_wholesale").is(':checked') == false)
        jQuery("#ws-selected-roles").parent().parent().hide();

    jQuery("#activate_wholesale").click(function() {
        if ($(this).is(':checked'))
            jQuery("#ws-selected-roles").parent().parent().show();
        else
            jQuery("#ws-selected-roles").parent().parent().hide();
    });
    
    const tabHeader = document.querySelectorAll(".tab-header li");

    for (const elem of tabHeader) {
        elem.addEventListener("click", function() {
            if (this.classList.contains('active')) return;

            document.querySelector(".tab-header li.active")?.classList.remove("active");
            this.classList.add("active");

            const pageId = this.getAttribute("tab-page");
            const page = document.getElementById(pageId);

            document.querySelector(".tab-page.active")?.classList.remove("active");
            page?.classList.add("active");
        });
    }

    const security = document.getElementById('security').value;
    const action = document.getElementById('action').value;
    const referralUrl = document.getElementById('referralUrl').value;
    const buttons = document.querySelectorAll('#request-call-list button');

    for(let button of buttons) {
        button.addEventListener('click', function() {
            const phoneNumber = this.getAttribute('phone-number');
            const data = { action: action, security: security, phoneNumbers: [ phoneNumber ] };
            this.classList.add('loading');
            removeRowPost(referralUrl, data, this);
        });
    }

    const clearAll = document.getElementById('clearAll');
    clearAll.addEventListener('click', function() {
        const data = {
            action: action,
            security: security,
            clearAll: true
        };
        removeRowPost(referralUrl, data); // remove all rows if success (third arg is null)
    });
    
    function removeRowPost(referralUrl, data, button = null) {
        jQuery.ajax({
            type: "POST",
            url: referralUrl,
            data: data,
            dataType: "json",
            fail: function (jqXHR, error) {
                console.log(error);
            },
            success: function (response) {
                const message = response?.message;
                const status = response?.status;
                button?.classList.remove('loading');
                
                if (status == 'success') {
                    if (button) {
                        jQuery(button.parentElement?.parentElement).fadeOut(500, function() { this.remove(); });
                    } else {
                        const callList = document.getElementById('request-call-list');
                        callList(...[...callList.children].slice(0,2));
                    }
                } else {
                    alert(message);
                }
            }
        });
    }
}