(function($) {
    "use strict";

    var $document = $(document);
    var $body = $('body');

    if(typeof adminpage !== 'undefined' && adminpage === 'tea-link-protection_page_tlp-edit-link') {
        var $menu = $('#toplevel_page_tlp-links');

        $menu.find('.current').remove();
        $menu.find('.wp-first-item').addClass('current');
    }

    $('[data-chain="group"]').each(function() {
        var $group = $(this);
        var $segments = $group.find('[data-chain="segment"]');
        var $triggers = $group.find('[data-chain="trigger"]');
        var $global_form_fields = $segments.find('input, select, textarea');

        $global_form_fields.attr('disabled', true);

        $triggers.each(function() {
            var $trigger = $(this);
            var $target = null;

            var target = $trigger.attr('data-target');

            if(target) {
                $target = $('#' + target);
            } else {
                $target = $trigger.closest('[data-chain="segment"]');
            }

            var $segment_form_fields = $target.find('input, select, textarea');

            // dry
            if($target.is(':visible')) {
                $segment_form_fields.removeAttr('disabled');
            }

            $trigger.on('click', function() {
                $segments.slideUp('fast');
                $target.slideToggle('fast', function() {
                    $global_form_fields.attr('disabled', true);

                    // dry
                    if($target.is(':visible')) {
                        $segment_form_fields.removeAttr('disabled');
                    }
                });
            });
        });
    });

    if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
        var $media = wp.media({
            multiple: false
        });

        // On select attachment callback
        $media.on('select', function() {
            var attachment = $media.state().get('selection').first().toJSON();

            $media.storage.find('#attachment-url').val(attachment.url);
            $media.storage.find('#attachment-id').val(attachment.id);
        });

        // Open media box
        $document.on('click', '[data-behaviour="media-open"]', function() {
            var $this = $(this);
            var $storage = $('#' + $this.attr('data-storage'));

            $media.storage = $storage;
            $media.open();
        });
    }

})(jQuery);