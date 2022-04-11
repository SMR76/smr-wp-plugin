document.addEventListener("DOMContentLoaded", () => {
    const form = jQuery('#sms-contact-form');
    form.submit(function (event) {
        event.preventDefault();

        let data = jQuery(this).serializeArray();

        jQuery.ajax({
            type: "POST",
            url: this.referralUrl.value,
            data: data,
            dataType: "json",
            fail: function (jqXHR, error) {
                alertFactory(error, 'danger', form);
            },
            success: function (response) {
                const message = response?.message ?? response;
                const status = response?.status ?? 'danger';
                alertFactory(message, status, form, message.match(/^(\d|\s)*[a-z]/i) ? 'ltr' : 'rtl');
            }
        });
        return false;
    });
});

function alertFactory(text, type, parent, dir = 'ltr') {
    let element = jQuery(`<div class="col-12 mb-1"><div class="alert alert-${type}" style="direction: ${dir}">${text}</div></div>`);
    element.hide();
    parent.append(element);
    element.fadeIn();
    setTimeout(() => {
        element.fadeOut(400, () => element.remove());
    }, 6000); // remove after 3.4 sec
}
