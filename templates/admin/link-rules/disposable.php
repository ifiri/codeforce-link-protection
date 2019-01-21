<?php
$is_disposable = false;
if(isset($rules['DISPOSABLE']) && $rules['DISPOSABLE'] === 'on') {
    $is_disposable = true;
}
?>
<div class="tlp-field-group">
    <label class="tlp-form__label tlp-field">
        <span class="tlp-field__title">
            <?php _e('Disposable Link', 'tea-link-protection') ?>
        </span>

        <br>
        
        <input type="hidden" name="<?php echo $alias ?>" value="off">
        <input type="checkbox" class="tlp-field__input tlp-field__input_type_checkbox" name="<?php echo $alias ?>"
            <?php if($is_disposable) :
                echo 'checked';
            endif; ?>
        >

        <small class="tlp-field__description">
            <?php _e('Regenerate the link after one triggering', 'tea-link-protection') ?>
        </small>
    </label>
</div>