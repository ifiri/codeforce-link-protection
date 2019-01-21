<div class="tlp-chain-segment" id="<?php echo $alias ?>-link-group" data-chain="segment"
        <?php if(!$is_attachment) :
            echo 'style="display: none"';
        endif; ?>
    >
    <div class="tlp-field-group tlp-<?php echo $alias ?>-info" id="<?php echo $alias ?>-info">

        <label class="tlp-form__label tlp-field">
            <span class="tlp-field__title">
                <?php _e('Select attachment', 'tea-link-protection') ?>
            </span>

            <br>

            <input type="text" class="regular-text tlp-field__input" id="<?php echo $alias ?>-url"
            value="<?php
                if($is_attachment && !$is_new_link) :
                    echo $link_data['link'];
                endif;
            ?>" 
            name="link" readonly>
        </label>

        <input type="button" class="button" value="<?php _e('Select', 'tea-link-protection') ?>" data-behaviour="media-open" data-storage="<?php echo $alias ?>-info">

        <input type="hidden" name="attachment_id" id="<?php echo $alias ?>-id">
    </div>
</div>