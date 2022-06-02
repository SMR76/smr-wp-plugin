

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("ws_active").onclick = function() {
        const row = document.getElementById("ws_roles")?.parentElement?.parentElement;
        row.style.display = this.checked ? "" : "none";
    };

    document.getElementById("sticky_activate").onclick = function() {
        document.getElementById("instagram_text").disabled = !this.checked;
        document.getElementById("call_text").disabled = !this.checked;
        document.getElementById("whatsapp_text").disabled = !this.checked;
    };

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
    const buttons = document.querySelectorAll('#sms-contact-list button');

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
        if(confirm('Are you sure you want to clear all contacts?') == false) {
            const data = {
                action: action,
                security: security,
                clearAll: true
            };
            removeRowPost(referralUrl, data); // remove all rows if success (third arg is null)
        }
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
                        const callList = document.getElementById('sms-contact-list');
                        callList.replaceChildren(...[...callList.children].slice(0,2));
                    }
                } else {
                    alert(message);
                }
            }
        });
    }
});