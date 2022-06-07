var global = {
    productList: [],
    modifiedProductsList: [],
    taxonomiesList: [],
    updateCount: 0,
};

/**
 * @param {Number} start
 * @param {Number} end
 * @param {String} target
 * @returns {String}
 */
String.prototype.replaceRange = function (start, end, target) {
    if (end < start)[start, end] = [end, start]; //> swap if end > start
    return this.slice(0, start) + target + this.slice(end);
}

document.cookie = "SameSite=Strict; Secure";

document.addEventListener("DOMContentLoaded", () => {
    const queryBox = document.getElementById("querybox");
    const taxonomies = document.getElementById("taxonomies");
    const checkall = document.getElementById("checkall");

    checkall.onclick = () => {
        const productList = document.getElementById("productsList");
        productList?.childNodes?.forEach(node => {
            const checkbox = node?.children?.status?.children?.checkbox;
            if(checkbox) checkbox.checked = checkall.checked
        });
    }

    queryBox.addEventListener('keyup', onQueryBoxChanged);
    initializeActionsEvent();

    sendCommand("getTaxonomies").then((taxonomiesList) => {
        global.taxonomiesList = taxonomiesList;
        // Set taxonomy variables
        taxonomies.innerHTML += Object.entries(taxonomiesList).reduce((a, el) => {
            return a + `<label class="var" value='"${ el[0] }"'>${ el[1].slug }(${ el[0] })</label>`;
        }, "");

        statusLogger("Updating products html...", "info");
        sendCommand("getProducts").then((productsList) => {
            global.productList = productsList;
            updateProductsHtml(global.productList);
            statusLogger("Updating products html done.", "info");
        });

        queryBox.removeAttribute("disabled");
        initialVarsEvent();
    });
});

/**
 * @param {Event} event
 */
function onQueryBoxChanged(event) {
    if(["Enter", "NumpadEnter", "Delete", "Backspace", "Tab", "Space"].includes(event.code)) {
        event.target.rows = event.target.value.split(/\r\n|\r|\n/).length + 1;
    }
    runQuery(); //> Execute query on queryBox change.
}

function initialVarsEvent() {
    const queryBox = document.getElementById("querybox");
    const vars = document.getElementsByClassName("var");

    [...vars].forEach((el) => el.addEventListener("click", function() {
        queryBox.value = queryBox.value.replaceRange(queryBox.selectionStart, queryBox.selectionEnd, this.getAttribute("value"));
        queryBox.focus();
        runQuery(); //> Execute query on queryBox change.
    }));
}

function refreshProducts() {
    const productsListElem = document.getElementById('productsList');
    productsListElem.innerHTML = '<div class="row"><div class="loader" style="margin: 10px auto;"></div></div>';

    statusLogger("Updating products html...", "info");
    sendCommand("getProducts").then((productsList) => {
        global.modifiedProductsList = [];
        global.productList = productsList;
        updateProductsHtml(global.productList);
        statusLogger("Updating products html done.", "info");
    });
}

function initializeActionsEvent() {
    const queryBox = document.getElementById("querybox");
    const sendAction = document.getElementById("send");
    const clearAction = document.getElementById("clear");
    const refreshAction = document.getElementById("refresh");

    clearAction.onclick = () => {
        queryBox.value = "";
    };

    refreshAction.onclick = refreshProducts;

    sendAction.onclick = () => {
        const productsElements = document.getElementById("productsList").children
        const selectedIds = [...productsElements].filter( el => el.children?.status?.children?.checkbox?.checked )
                                                 .map(el => parseInt(el.getAttribute('pid'))) || [];

        if(global.modifiedProductsList?.length < 1 || selectedIds.length < 1) {
            statusLogger("No products to send.");
            return;
        }
        const result = confirm("Sure you want to continue?\n* This action cannot be undone.");
        if(result === true) {
            global.updateCount = 0;

            for(const pid of selectedIds) {
                const product = global.modifiedProductsList[pid];
                if(product) {
                    setRowLoading(pid, true);
                    sendCommand("updateProduct", { id: pid, product: product}).catch(() => {
                        statusLogger(`Error updating product ${pid}.`);
                    }).finally(() => {
                        setRowLoading(pid, false);
                        global.updateCount += 1;
                        global.modifiedProductsList[pid] = null;

                        if(global.updateCount === selectedIds.length) {
                            statusLogger("All products updated.", "success");
                            refreshProducts();
                        }
                    });
                }
            }
        }
    };
}

var timerId = null; //> stores timer id

function statusLogger(text, type = "error", timeout = 6000) {
    const statusElement = document.getElementById("status");
    statusElement.style.backgroundColor = type === 'error' ? '#f001' : '#08f1';
    statusElement.innerHTML = text;
    statusElement.classList.add("show");
    if(timerId) clearTimeout(timerId);
    timerId = setTimeout(() => statusElement.classList.remove("show"), timeout);
}

function runQuery() {
    const queryBox = document.getElementById("querybox");
    const query = queryBox.value;
    global.modifiedProductsList = []; //> reset modified products list

    for(const pid in global.productList) {
        const productRow = document.querySelector(".row[pid='" + pid + "']") || {};
        const element = productRow.children?.pinfo?.children?.modified;
        if(element) element.innerHTML = "";

        const result = queryExecuter(pid, query);

        if(result === false) break;

        if(element && Object.keys(result).length) { //> if modified element exists and object is not empty
            global.modifiedProductsList[pid] = result;
            const price = result.hasOwnProperty("regular_price") ? priceHtml(result.regular_price, result.price) : "";
            const taxonomies = (result.taxonomies || []).map(tid => taxonomiesHtml(tid)).join("");

            element.innerHTML = gridRowHtml('', '', '', price , taxonomies);
        }
    }
}

/**
 * @param {Number} pid // The product id
 * @param {String} query // The query
 */
function queryExecuter(pid, query) {
    const product = global.productList[pid];
    const taxonomies = product.taxonomies || [];

    if(!product || !product.hasOwnProperty("regular_price") || !product.hasOwnProperty("price")
                || !product.hasOwnProperty("post_name") || !product.hasOwnProperty("guid")) {
        return {};
    }

    let result = {};
    let sprice, rprice, txnmy;

    try {
        const queryFunction = new Function( "id", "name", "url", "regularPrice", "salePrice", "taxonomies", query + ";return [regularPrice,salePrice,taxonomies];");
        [rprice,sprice,txnmy] = queryFunction(pid, product.post_name, product.guid,
                                            parseInt(product.regular_price),
                                            parseInt(product.price),
                                            [...taxonomies]);
        txnmy = [...new Set(txnmy)]; //> Remove duplicates
    } catch (e) {
        statusLogger(`ProductID: ${ pid }<br>${ e }`);
        return false;
    }

    if(rprice != product.regular_price || sprice != product.price) {
        result.regular_price = rprice;
        result.price = rprice;
        if(sprice != product.price || product.price != product.regular_price) {
            result.price = sprice;
        }
    }

    if(txnmy.length !== taxonomies.length ||
        txnmy.some((t, i) => t !== taxonomies[i])) {
        result.taxonomies = txnmy;
    }

    return result;
}

/**
 * @param {String} url
 * @param {*} data
 * @param {Function} onready // On ready callback function.
 * @param {Function} onerror // On error callback function.
 */
function postJson(url, data, requestHeaders, onready, onerror) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    Object.keys(requestHeaders).forEach(header => xhr.setRequestHeader(header, requestHeaders[header]));
    xhr.onerror = onerror;
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            onready(JSON.parse(this.responseText));
        }
    }
    xhr.send(JSON.stringify(data));
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

        // replace vanillaJS & jQuery
        // postJson(referralUrl, {command, ...exteraData}, {action, security}, (response) => {
        //     if(response?.success == true) {
        //         callback(response?.data);
        //     }
        // });
        jQuery.ajax({
            type: "POST",
            url: referralUrl,
            data: { action: action, security: security, command: command, ...exteraData },
            dataType: "json",
            fail: function (jqXHR, error) {
                statusLogger("Ajax Error: " + error);
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

/**
 * @param {Number} number
 * @returns {String}
 */
function toLocalStr(number) {
    return Number(number).toLocaleString();
}

/**
 * @param {Number} regularPrice // The product regular price
 * @param {Number} salePrice // The product sale price
 * @returns {String} // The product price as html
 */
function priceHtml(regularPrice, salePrice) {
    if(regularPrice !== undefined && salePrice !== undefined) {
        return regularPrice == salePrice ? toLocalStr(regularPrice) : `${ toLocalStr(salePrice) }/<s>${ toLocalStr(regularPrice) }</s>`;
    } else {
        return "";
    }
}

/**
 * @param {Number} tid
 * @returns
 */
function getContrast(hexColor) {
    const [r, g, b] = [1, 3, 5].map(p => parseInt( hexColor.substr( p, 2 ), 16 ));
    return (r * 299 + g * 587 + b * 114) / 1000;
}

/**
 * @param {Number} tid
 * @returns {String}
 */
function taxonomiesHtml(tid) {
    const taxonomy = global.taxonomiesList[tid];
    if(taxonomy && taxonomy.meta_key) {
        const hexColor = taxonomy.meta_key == "color" ? taxonomy.meta_value : "#ffffff";
        const colorTheme = getContrast(hexColor) > 125 ? "light" : "dark";
        const style = taxonomy.meta_key == "color" ? `style="background-color:${ taxonomy.meta_value }"` : "";
        return `<span class="taxonomy ${ colorTheme }" ${ style }>${ taxonomy.name }</span>`;
    }
}

/**
 * @param {Number} id // The product id
 * @param {String} pname // The product name
 * @param {String} aname // The author name
 * @param {String} price // The product price
 * @param {String} taxonomies // The product taxonomies
 * @returns {String}
 */
function gridRowHtml(id, pname, aname, price, taxonomies) {
    return `<div class="col-1">${ id }</div>` +
           `<div class="col-4">${ pname }</div>` +
           `<div class="col-2">${ aname }</div>` +
           `<div class="col-2">${ price }</div>` +
           `<div class="col-3">${ taxonomies }</div>`;
}

/**
 * @param {Number} pid
 * @param {Boolean} enable
 */
function setRowLoading(pid, enable = true) {
    const productRow = document.querySelector(`.row[pid="${ pid }"]`);
    const element = productRow.children?.status?.children?.loader;
    element.classList.toggle("hidden", !enable);
}

/**
 * @param {Array} productsList
 */
async function updateProductsHtml(productsList) {
    const productsListElem = document.getElementById('productsList');
    const modifiedProductsList = global.modifiedProductsList;

    productsListElem.innerHTML = "";

    for(const [id, product] of Object.entries(productsList)) {
        const postName = decodeURI(product.post_name);
        const productUrl = decodeURI(product.guid);
        const productName = `<a href="${ productUrl }">${ postName }</a><br><a href="/wp-admin/post.php?post=${id}&action=edit">edit</a>`;
        const price = priceHtml(product.regular_price, product.price);
        const _taxonomies = product.taxonomies || [];
        const taxonomies = _taxonomies.map(tid => taxonomiesHtml(tid)).join("");

        const mproduct = modifiedProductsList[id] || {};
        const mprice = priceHtml(mproduct.regular_price, mproduct.price);
        const _mtaxonomies = mproduct.taxonomies || [];
        const mtaxonomies = _mtaxonomies.map(tid => taxonomiesHtml(tid)).join("");

        const rowHtml =
            `<div class="row" pid="${ id }">
                <div class="col-1 d-flex align-center justify-space-around" name="status">
                    <input type="checkbox" name="checkbox">
                    <div class="loader hidden" name="loader"></div>
                </div>
                <div class="col-11 pinfo" name="pinfo">
                    <div name="current" class="row">${ gridRowHtml(id, productName, '', price, taxonomies) }</div>
                    <div name="modified" class="row">${ (modifiedProductsList[id] ? gridRowHtml('', '', '', mprice, mtaxonomies) : "") }</div>
                </div>
            </div>`;

        productsListElem.innerHTML += rowHtml;
    }
}