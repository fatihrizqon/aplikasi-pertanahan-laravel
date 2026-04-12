globalThis.objectFlat = function (object) {
    const getEntries = (data, prefix = "") =>
        Object.entries(data).flatMap(([key, val]) =>
            Object(val) === val
                ? getEntries(val, `${prefix}${key}.`)
                : [[`${prefix}${key}`, val]],
        );
    return Object.fromEntries(getEntries(object));
};

globalThis.dump = function () {
    Array.from(arguments).forEach((i) => console.debug(i));
};

globalThis.unique = (prefix, length) => {
    prefix ??= "";
    length ??= 36;
    return `${prefix}${Date.now()}`.toString(length);
};

globalThis.empty = function () {
    return Array.from(arguments).every((e) => {
        if (e === null) {
            return true;
        }
        if (typeof e === "undefined") {
            return true;
        }
        if (typeof e === "boolean" && e == false) {
            return true;
        }
        if (typeof e === "string" && e.trim() === "") {
            return true;
        }
        return false;
    });
};

globalThis.slug = (str, len = 256) => {
    return str
        .replace(/[^a-zA-Z0-9]/g, "-")
        .replace(/-{2,}/g, "-")
        .replace(/^-{1,}/, "")
        .replace(/-{1,}$/, "")
        .substr(0, len)
        .trim();
};

globalThis.debounce = (func, wait, immediate) => {
    var timeout;
    return function () {
        let context = this,
            params = arguments;
        let fn_later = function () {
            timeout = null;
            if (!immediate) func.apply(context, params);
        };
        let callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(fn_later, wait);
        if (callNow) func.apply(context, params);
    };
};

globalThis.jsonFromScript = function (elem) {
    if ($.isPlainObject(elem)) {
        return elem;
    }
    if (typeof elem === "string") {
        elem = document.querySelector(elem);
    }
    if (elem && elem.tagName === "SCRIPT" && elem.type === "application/json") {
        return JSON.parse(elem.textContent);
    }
    return null;
};

globalThis.jsonScriptToFormFields = function (formElem, jsonData) {
    jsonData = jsonFromScript(jsonData);

    if (typeof formElem === "string") {
        formElem = document.querySelector(formElem);
    }

    if (!formElem || !jsonData) return;

    let foundFields = [];
    for (let name in jsonData) {
        $(formElem)
            .find(`[name="${name}"]`)
            .each(function () {
                foundFields.push(this);
                if (
                    this.tagName === "INPUT" &&
                    /radio|checkbox/.test(this.type)
                ) {
                    if (this.value == jsonData[name]) {
                        this.checked = true;
                    }
                } else if (this.type !== "file") {
                    this.value = jsonData[name] ?? "";
                }
            });
    }

    const flatData = objectFlat(jsonData);
    $(formElem)
        .find("[name]")
        .each(function () {
            if (foundFields.includes(this)) return;

            let name = this.name
                .replace("][", ".")
                .replace("[", ".")
                .replace("]", "");
            if (name in flatData) {
                if (
                    this.tagName === "INPUT" &&
                    /radio|checkbox/.test(this.type)
                ) {
                    if (this.value === flatData[name]) {
                        this.checked = true;
                    }
                } else {
                    this.value = flatData[name] ?? "";
                }
            }
        });

    return jsonData;
};

class FormFieldMultiple {
    constructor(options) {
        this.models = [];
        this.actions = {};
        this.modelCallback = $.noop;
        this.modelFieldCallback = $.noop;
        this.fieldPrefix;
        this.fnUnique = () => {
            this.fnUniqueCounter ??= 0;
            this.fnUniqueCounter++;
            return (Date.now() + this.fnUniqueCounter).toString(36);
        };
        this.options(options);

        if (typeof options.wrapper === "string") {
            options.wrapper = document.querySelector(options.wrapper);
        }
        this.wrapper = options.wrapper;

        let template = options.wrapper.firstElementChild;
        this.template = template.cloneNode(true);
        template.remove();

        this.models.forEach((model) => {
            this.render(model);
        });
        if (this.models.length === 0) {
            this.render();
        }
    }

    options(options) {
        options ??= {};
        for (const [k, v] of Object.entries(options)) {
            this[k] = v;
        }
    }

    model(model) {
        model ??= {};
        model.id ??= null;
        return model;
    }

    render(model) {
        let unique = this.fnUnique();
        model = this.model(model);
        model.unique = unique;

        let that = this;
        let template = this.template.cloneNode(true);

        // make
        $(template).appendTo(this.wrapper).attr("id", unique);
        $(template)
            .find("[data-action]")
            .each(function () {
                $(this).on("click", function () {
                    that.actions[this.dataset.action].call(
                        that,
                        this,
                        template,
                        model,
                    );
                });
            });

        // fill
        $(template)
            .find("[name]")
            .each(function () {
                let element = this;

                let key = element.name;
                let val = model[key] ?? null;

                element.name = `${that.fieldPrefix}[${
                    model.id ?? unique
                }][${key}]`;
                element.value = val;
                element.dataset.field = key;

                // karena respon laravel begini, jadi harus disesuaikan.
                element.dataset.errorSelector = element.name
                    .replace("][", ".")
                    .replace("[", ".")
                    .replace("]", "");

                that.modelFieldCallback.call(that, element, model);
            });

        that.modelCallback.call(that, template, model);
    }
}

if (globalThis.FormFieldMultiple === undefined) {
    globalThis.FormFieldMultiple = FormFieldMultiple;
}

globalThis.windowDialog = (element, event, features) => {
    event?.preventDefault();

    let href = element.dataset.href || element.href;
    if (empty(href)) {
        console.error(
            `attribute href tidak ditemukan, pada element <${element.tagName}>!`,
        );
        return;
    }

    features ??= {};
    features = {
        ...{
            popup: 1,
            status: 0,
            location: 0,
            titlebar: 0,
            toolbar: 0,
            menubar: 0,
            width: 768,
            height: 432,
        },
        ...features,
    };

    window.open(
        href,
        element.target !== "" ? element.target : `dialog${Date.now()}`,
        Object.entries(features)
            .map(([k, v]) => `${k}=${v}`)
            .join(","),
    );
    return;
};

globalThis.modalFormAjax = async (el, e) => {
    e.preventDefault();

    if (el.tagName !== "A")
        return console.error(`Anchor tag <a> is required, not <${el.tagName}>`);

    const modal = document.querySelector("#modal-form-ajax");
    const content = modal?.querySelector("#modal-form-content");

    if (!content) return console.error("Modal content not found.");

    content.innerHTML = `<div class="p-6 text-center text-gray-500">Please wait...</div>`;
    window.HSOverlay.open(modal);

    try {
        const res = await fetch(el.href, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });

        if (res.status === 403) {
            content.innerHTML = `
            <div class="p-6 text-center justify-center">
                <div class="flex items-center pt-8 sm:justify-center sm:pt-0">
                    <h1 class="px-4 text-lg dark:text-gray-300 text-gray-700 border-r border-gray-400 tracking-wider">
                        403
                    </h1>
                    <div class="ml-4 text-lg dark:text-gray-300 text-gray-700 uppercase tracking-wider">
                        Forbidden
                    </div>
                </div>
            </div>
            `;
            return;
        }

        if (!res.ok) {
            content.innerHTML = `
                <div class="p-6 text-center text-red-500">
                    Failed to load the form.
                </div>
            `;
            return;
        }

        const html = await res.text();
        content.innerHTML = html;

        content.innerHTML = html;
        content.querySelectorAll("script").forEach((s) => {
            const sc = document.createElement("script");
            s.src ? (sc.src = s.src) : (sc.textContent = s.textContent);
            document.body.appendChild(sc).remove();
        });
    } catch (err) {
        content.innerHTML = `<div class="p-6 text-center text-red-500">Failed to open the modal.</div>`;
        console.error(err);
    }
};

globalThis.modalConfirm = function (el, e, c) {
    e.preventDefault();

    if (el.tagName !== "A")
        return console.error(`Anchor tag <a> is required, not <${el.tagName}>`);

    const modal = document.querySelector("#modal-confirm");
    const content = modal?.querySelector("#modal-confirm-content");
    if (!content) return console.error("Modal content not found.");

    const form = content.querySelector("form");
    if (!form) return console.error("Form not found inside modal content.");

    form.action = el.getAttribute("href");
    const method = el.dataset.method || "POST";
    const methodInput = form.querySelector('input[name="_method"]');
    console.log(methodInput);
    if (methodInput) methodInput.value = method;

    content.querySelector(".modal-header").innerHTML =
        `<h3 class="text-lg font-semibold">${
            el.dataset.title || "Confirmation"
        }</h3>`;
    content.querySelector(".modal-body").innerHTML =
        el.dataset.content || "Are you sure?";

    if (el.dataset.footer) {
        content.querySelector(".modal-footer").innerHTML = el.dataset.footer;
    }

    window.HSOverlay.open(modal);
};

globalThis.modalDeleteConfirm = function (elem, event, callback) {
    event?.preventDefault();

    if (elem.tagName != "A") {
        console.error(`harus pake <A>, bukan pake <${elem.tagName}>!`);
        return;
    }
    let modalElem = document.querySelector("#modal-delete-confirm");
    if (empty(modalElem)) {
        console.error(`element "#modal-delete-confirm" tidak ditemukan!`);
        return;
    }

    // init, regardless.
    $(modalElem).modal({
        show: false,
        focus: false,
        keyboard: false,
        backdrop: "static",
    });

    $(modalElem).find("form").prop("action", elem.href);

    $(modalElem)
        .find("[data-key]")
        .each(function () {
            let val = this.dataset.val;
            dump(elem, elem.dataset);
            if (this.dataset.key in elem.dataset) {
                val = elem.dataset[this.dataset.key];
            }

            $(this).text(val);
        });

    if (
        "bootstrap" in globalThis &&
        "Modal" in bootstrap &&
        "getInstance" in bootstrap.Modal
    ) {
        let bs5x = bootstrap.Modal.getInstance(modalElem);
        if (bs5x) {
            bs5x.show(elem);
        }
    } else {
        let bsv3v4 = $(modalElem).data("bs.modal");
        if (bsv3v4) {
            bsv3v4.show(elem);
        }
    }
};

document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const body = document.body;

    // Guard: jika sidebar layout tidak ada (misal di halaman peta), hentikan eksekusi
    if (!sidebar) return;

    function closeSidebarMobile() {
        sidebar.classList.add("-translate-x-full");
        if (overlay) overlay.classList.add("hidden");
    }

    function openSidebarMobile() {
        sidebar.classList.remove("-translate-x-full");
        if (overlay) overlay.classList.remove("hidden");
    }

    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            if (window.innerWidth >= 1024) {
                body.classList.toggle("sidebar-collapsed");
            } else {
                const isOpen = !sidebar.classList.contains("-translate-x-full");
                isOpen ? closeSidebarMobile() : openSidebarMobile();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener("click", closeSidebarMobile);
    }

    window.addEventListener("resize", () => {
        if (window.innerWidth >= 1024) {
            closeSidebarMobile();
        }
    });
});

document.querySelectorAll(".hs-dropdown-toggle").forEach((el) => {
    el.addEventListener("click", () => {
        // console.log("Dropdown clicked");
    });
});
