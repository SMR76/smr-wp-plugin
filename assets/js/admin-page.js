/**
 * @param {String} referralUrl
 * @param {*} data
 * @param {*} button
 */
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

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("ws_active").onclick = function() {
        const row = document.getElementById("ws_roles")?.parentElement?.parentElement;
        row.style.display = this.checked ? "" : "none";
    };

    // sticky button
    const stickyLeft = document.getElementById("stickyLeft");
    const stickyRight = document.getElementById("stickyRight");

    stickyLeft.onwheel = stickyRight.onwheel = function(event) {
        event.preventDefault();
        this.value = Number.parseInt(this.value) + event.deltaY * -0.01;
        this.value = Math.max(this.min, Math.min(this.value, this.max));
        this.dispatchEvent(new Event('change'));
    }

    if(stickyLeft && stickyRight) {
        // If one side's value changes, this lines will set the other side's value to zero.
        stickyLeft.onchange = function() {
            stickyRight.value = -1;
            stickyRight.classList.add("disable");
            this.classList.remove("disable");
        }

        stickyRight.onchange = function() {
            stickyLeft.value = -1;
            stickyLeft.classList.add("disable");
            this.classList.remove("disable");
        }
    }

    document.getElementById("sticky_activate").onclick = function() {
        document.getElementById("instagram_text").disabled = !this.checked;
        document.getElementById("call_text").disabled = !this.checked;
        document.getElementById("whatsapp_text").disabled = !this.checked;
    };

    // tabs
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

    // select media
    let selectedImage = document.getElementById("selectedImage");
    let selectedImageInput = document.getElementById("form_background_image");

    selectedImage.addEventListener("click", () => {
        frame = wp.media({
            title: "Select or Upload Media Of Your Chosen Persuasion",
            button: { text: "Use this media" },
            multiple: false,
        });

        frame.on("select", () => {
            let attachment = frame.state().get("selection").first().toJSON();
            selectedImage.src = attachment.url;
            selectedImageInput.value = attachment.url;
        });

        frame.open();
    });
});