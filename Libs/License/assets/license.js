
jQuery(function($) {

    class Copy_To_Clipboard_License_Manager {

        constructor($widget) {
            this.$widget = $widget;
            this.redirectTimer = 1500;
            this.setEvents();
        }

        setEvents() {

            const that = this;

            this.$widget.on('click', '.copy-to-clipboard-license-deactivate', function(e) {
                e.preventDefault();
                that.deactivate();
            });

            this.$widget.on('click', '.copy-to-clipboard-license-activate', function(e) {
                e.preventDefault();
                that.activate();
            });

        }

        deactivate() {

            var deactivate_action = this.$widget.find('input[name=deactivate_action]').val();
            var nonce = this.$widget.find('input[name=nonce]').val();
            this.$widget.find('.copy-to-clipboard-license-popup--notice').slideUp(100);
            this.$widget.find('.copy-to-clipboard-license--loader-wrapper').addClass('active');

            let data = {
                nonce: nonce,
                action: deactivate_action
            };

            jQuery.ajax({
                method: 'POST',
                url: Copy_To_Clipboard_License_Manager_Vars.ajaxurl,
                data: data
            }).then((res) => {
                if (res.success && res.data.message) {
                    this.displayNotice(res.data.message, 'success');
                    this.$widget.removeClass('copy-to-clipboard-license-status--active');
                    setTimeout(function() {
                        window.location.reload();
                    }, this.redirectTimer);
                } else if (res.data.message) {
                    this.displayNotice(res.data.message, 'error');
                }
                this.$widget.find('.copy-to-clipboard-license--loader-wrapper').removeClass('active');
            }).catch((error) => {
                this.displayNotice(error, 'error');
                this.$widget.find('.copy-to-clipboard-license--loader-wrapper').removeClass('active');
            });
        }

        activate() {

            this.$widget.find('.copy-to-clipboard-license-popup--notice').slideUp(100);
            this.$widget.find('.copy-to-clipboard-license--loader-wrapper').addClass('active');

            let data = {
                license_key: this.$widget.find('.license_key').val(),
                nonce: this.$widget.find('input[name=nonce]').val(),
                action: this.$widget.find('input[name=activate_action]').val()
            }

            if (this.$widget.find('.product_id').length) {
                data.product_id = this.$widget.find('.product_id').val();
            }

            jQuery.ajax({
                method: 'POST',
                url: Copy_To_Clipboard_License_Manager_Vars.ajaxurl,
                data: data
            }).then((res) => {
                if (res.success && res.data.message) {
                    this.displayNotice(res.data.message, 'success');
                    if (res.data.activated_text) this.$widget.find('.copy-to-clipboard-license-status td').text(res.data.activated_text);
                    this.$widget.addClass('copy-to-clipboard-license-status--active');
                    setTimeout(function() {
                        Copy_To_Clipboard_License_Manager_Vars.redirect_url ? window.location = Copy_To_Clipboard_License_Manager_Vars.redirect_url : window.location.reload();
                    }, this.redirectTimer);
                } else if (res.data.message) {
                    this.displayNotice(res.data.message, 'error');
                }
                this.$widget.find('.copy-to-clipboard-license--loader-wrapper').removeClass('active');
            }).catch((error) => {
                this.displayNotice(error, 'error');
                this.$widget.find('.copy-to-clipboard-license--loader-wrapper').removeClass('active');
            });

        }

        displayNotice(message, type) {
            if (!message) return;
            if (type == 'success') {
                this.$widget.find('.copy-to-clipboard-license-popup--notice').removeClass('copy-to-clipboard-license--notice-error').addClass('copy-to-clipboard-license--notice-success').html(message).slideDown(100);
            } else {
                this.$widget.find('.copy-to-clipboard-license-popup--notice').removeClass('copy-to-clipboard-license--notice-success').addClass('copy-to-clipboard-license--notice-error').html(message).slideDown(100);
            }
        }

    }

    // Initialize Popup
    $('.copy-to-clipboard-license-popup').each(function() {
        new Copy_To_Clipboard_License_Manager($(this));
    });

    // Open Popup
    $('.copy-to-clipboard-lpt-btn').on('click', function(e) {
        e.preventDefault();
        $('#' + $(this).data('wc-popup-target')).find('.copy-to-clipboard-license-popup--wrapper').fadeIn(100);
    });

    // Close Popup
    $('.wc-licelse--close').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.copy-to-clipboard-license-popup--wrapper').fadeOut(100);
    });

});