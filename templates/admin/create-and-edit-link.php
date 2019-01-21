<?php
$action = 'create-new-link';
if(!$is_new_link) {
    $action = 'update-link';
}
?>
<div class="wrap">
    <h1>
        <?php 
        if($is_new_link) :
            _e('Create new link', 'tea-link-protection');
        else :
            _e('Edit link', 'tea-link-protection');
        endif; 
        ?>
    </h1>

    <form method="post" class="tlp-form" autocomplete="off">
        <fieldset class="tlp-fieldset" data-chain="group">
            <legend>
                <?php _e('Link Type', 'tea-link-protection') ?>
            </legend>
            
            <div class="tlp-field-group" id="link-type-group">
                <?php foreach ($type_views as $TypeView) : ?>
                    <?php $TypeView->display(); ?>
                <?php endforeach; ?>
            </div>

            <div class="tlp-chain-segments">
                <?php foreach ($type_chain_segments as $ChainSegmentView) : ?>
                    <?php $ChainSegmentView->display(); ?>
                <?php endforeach; ?>
            </div>
        </fieldset>
        
        <fieldset class="tlp-fieldset">
            <legend>
                <?php _e('Link Attributes', 'tea-link-protection') ?>
            </legend>

            <?php foreach ($rule_views as $RuleView) : ?>
                <?php $RuleView->display(); ?>
            <?php endforeach; ?>
        </fieldset>

        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php _e('Submit', 'tea-link-protection') ?>">
            <input type="hidden" name="action" value="<?php echo $action ?>">
        </p>

        <?php wp_nonce_field($action); ?>
    </form>
</div>