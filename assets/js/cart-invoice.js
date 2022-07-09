'use strict';

/**
 *
 */
function createCartInvoice(invoiceButton) {
    let invoiceWindow = null;

    invoiceButton.classList.add("loading");

    sendCommand("getUserCartInvoice").then((data) => {
        invoiceButton.classList.remove("loading");

        invoiceWindow = window.open("", "Cart Invoice", "popup");
        invoiceWindow.resizeTo(920, 1080);
        invoiceWindow.document.open();
        invoiceWindow.document.title = "Cart Invoice";
        invoiceWindow.document.write(data);
        invoiceWindow.print();
    });
}

/**
 * @param {Strign} command // The command to send to the server
 * @param {Function} callback // The callback function
 */
async function sendCommand(command, exteraData = {}) {
    return new Promise((resolve, reject) => {
        const security = document.getElementById('security').value;
        const action = document.getElementById('action').value;
        const referralUrl = document.getElementById('referralUrl').value;

        jQuery.ajax({
            type: "POST",
            url: referralUrl,
            data: { action: action, security: security, command: command, ...exteraData },
            dataType: "json",
            fail: function (jqXHR, error) {
                console.log(error);
            },
            success: function (response) {
                let status = response?.success;
                if(status == true) {
                    resolve(response?.data);
                } else {
                    reject(response?.data);
                }
            }
        });
    });
}