$.fn.formAjaxSubmit = function (options) {
    options ??= {};

    const viewErrors = function (formElement, errors) {
        let foundFields = [];
        for (let name in errors) {
            $(formElement)
                .find(`[name]`)
                .each(function () {
                    const fieldElem = this;
                    if (
                        name === fieldElem.name ||
                        name === fieldElem.dataset.errorName
                    ) {
                        foundFields.push(name);
                        $(fieldElem).addClass("is-invalid");
                        let errorElem =
                            $(fieldElem).siblings(".invalid-feedback");
                        if (errorElem) {
                            $(errorElem).html(errors[name]);
                        } else {
                            fieldElem.title = errors[name];
                        }
                    }
                });
        }

        let voidErrors = {};
        let notFoundFields = Object.keys(errors).filter(
            (e) => !foundFields.includes(e)
        );
        notFoundFields.forEach((field) => {
            voidErrors[field] = errors[field];
        });
        if (notFoundFields.length > 0) {
            alert(JSON.stringify(voidErrors));
        }
    };

    this.on("submit", function (event) {
        event.preventDefault();
        const formElement = this;
        const submitButton = $("button[type=submit]");

        submitButton.attr("disabled", "disabled");
        $(formElement)
            .find(`[name]`)
            .each(function () {
                const fieldElem = this;

                $(fieldElem)
                    .removeClass("is-valid")
                    .removeClass("is-invalid")
                    .removeAttr("title");
            });

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: formElement.action,
            method: formElement.method,
            dataType: "json",
            data: $(formElement).serialize(),
            xhrFields: {
                withCredentials: true,
            },
        })
            .done(function (_data, _textStatus, _jqXHR) {
                if (options.doneCallback) {
                    options.doneCallback({
                        ...arguments,
                        formElement: formElement,
                    });
                } else {
                    formElement.submit();
                }
            })
            .fail((jqXHR, _textStatus, _errorThrown) => {
                if (jqXHR.status === 200) {
                    if (options.doneCallback) {
                        options.doneCallback({
                            ...arguments,
                            formElement: formElement,
                        });
                    } else {
                        submitButton.attr("disabled", "disabled");
                        formElement.submit();
                    }
                }

                if (
                    jqXHR.status === 422 &&
                    jqXHR.responseJSON &&
                    jqXHR.responseJSON.errors
                ) {
                    if (options.failCallback) {
                        options.failCallback({
                            ...arguments,
                            formElement: formElement,
                        });
                    } else {
                        submitButton.removeAttr("disabled");
                        viewErrors(formElement, jqXHR.responseJSON.errors);
                    }
                }
            })
            .always(() => {
                $(formElement)
                    .find(`[name]:not(.is-invalid)`)
                    .each(function () {
                        const fieldElem = this;
                        $(fieldElem).addClass("is-valid");
                    });
            });
    });
};

$.fn.pjaxCreate = function (options) {
    options ??= {};

    let container = this.get(0);
    if (empty(container.id)) {
        console.error("container pjax harus punya ID!");
        return;
    }

    $(container, document).on(
        "click",
        `a[href]:not([data-pjax="0"])`,
        (event) => {
            $.pjax.click(event, `#${container.id}`, options);
        }
    );

    $(container, document).on(
        "submit",
        `form[action]:not([data-pjax="0"])`,
        (event) => {
            $.pjax.submit(event, `#${container.id}`, options);
        }
    );
};

$('[data-bs-toggle="tooltip"]', document.body).each(function () {
    $(this).tooltip();
});
