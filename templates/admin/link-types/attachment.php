<label class="tlp-form__label tlp-field tlp-field_type_checkbox">
    <span class="tlp-field__title">
        <?php _e('Attachment', 'tea-link-protection') ?>
    </span>

    <input type="radio" class="regular-text tlp-field__input" name="link_type" id="<?php echo $alias ?>-link-type" value="<?php echo $alias ?>" data-chain="trigger" data-target="<?php echo $alias ?>-link-group"
        <?php if($is_attachment) :
            echo 'checked';
        endif; ?>
    >
</label>