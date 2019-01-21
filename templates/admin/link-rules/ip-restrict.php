<div class="tlp-field-group">
    <label class="tlp-form__label tlp-field">
        <span class="tlp-field__title">
            <?php _e('IP Restriction', 'tea-link-protection') ?>
        </span>

        <br>
        
        <input type="text" class="regular-text tlp-field__input" name="<?php echo $alias ?>" 
        value="<?php if(isset($rules['IP_RESTRICT'])) :
            echo implode(', ', $rules['IP_RESTRICT']);
        endif; ?>">
        
        <br>
        
        <small class="tlp-field__description">
            <?php _e('Enter one or multiple IPs, separated by commas. Subnet restriction and masks are allowed.', 'tea-link-protection') ?>
        </small>
    </label>
</div>