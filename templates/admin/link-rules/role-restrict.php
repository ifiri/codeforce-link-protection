<?php 
$selected_roles = [];
if(isset($rules['ROLE_RESTRICT'])) {
    $selected_roles = $rules['ROLE_RESTRICT'];
}
?>
<div class="tlp-field-group">
    <label class="tlp-form__label tlp-field">
        <span class="tlp-field__title">
            <?php _e('Role Restriction', 'tea-link-protection') ?>
        </span>
        
        <br>

        <select name="<?php echo $alias ?>[]" class="tlp-field__input tlp-field__input_type_select" multiple>
            <?php foreach ($roles as $role_alias => $role_data) : ?>
                <option value="<?php echo $role_alias ?>"
                    <?php if(in_array($role_alias, $selected_roles)) :
                        echo 'selected';
                    endif; ?>
                ><?php echo $role_data['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <br>

        <small class="tlp-field__description">
            <?php _e('Select roles which will <b>have</b> access to link (if nothing selected, all roles will have access)', 'tea-link-protection') ?>
        </small>
    </label>
</div>