<div class="tlp-field-group tlp-chain-segment" id="<?php echo $alias ?>-link-group" data-chain="segment"
        <?php if(!$is_common) :
            echo 'style="display: none"';
        endif; ?>
    >
    <label class="tlp-form__label tlp-field">
        <span class="tlp-field__title">
            <?php _e('Type the link address', 'tea-link-protection') ?>
        </span>

        <br>

        <input type="text" class="regular-text tlp-field__input" name="link" 
        value="<?php if($is_common && !$is_new_link) :
            echo $link_data['link'];
        endif; ?>"
        >
    </label>
</div>