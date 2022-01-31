"use strict";

new (function () {
    let self = this;

    this.reguest = function (data, method, success) {

        jQuery.post(
            wl_test_inf.url,
            {
                action: 'wl_test',
                method: method,
                data: data
            },
            success);
    };

    this.getFormData = function (form) {
        let obj = jQuery(form).serializeArray(),
            data = {};
        for (let i = obj.length - 1; i >= 0; i--) {
            data[obj[i].name] = obj[i].value;
        }
        return data;
    };
    this.onFormSubmit = function (form, form_type) {
        let data = self.getFormData(form);

        for (let i in data) {
            if (!data[i].length) return;
        }

        self.reguest(
            data,
            form_type,
            function (response) {
                if (response.status==true) {
                    jQuery('#wl_test').html('<div class="alert alert-success">'+response.status_message+'</div>');
                } else {
                    jQuery('#wl_test').append('<div class="alert alert-danger">'+response.status_message+'</div>');
                    setTimeout(function(){
                        jQuery('#wl_test .alert').remove();
                    }, 5000);
                }
            }
        );
    };
    jQuery(document).ready(function(){
        if (jQuery('#wl_test').length) {
            let form_login = jQuery('#wl_test_login form'),
                form_register = jQuery('#wl_test_register form');

            form_login.on('submit', function (e) {
                if (typeof e.preventDefault=='function') e.preventDefault();
                if (typeof e.stopPropagation=='function') e.stopPropagation();
                self.onFormSubmit(this, 'login');
                return false;
            });

            form_register.on('submit', function (e) {
                if (typeof e.preventDefault=='function') e.preventDefault();
                if (typeof e.stopPropagation=='function') e.stopPropagation();
                self.onFormSubmit(this, 'register');
                return false;
            });

        }
    });
})();